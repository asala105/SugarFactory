<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserConnection;
use App\Models\UserBlocked;
use App\Models\UserPicture;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;  //to validate date of birth
use Illuminate\Support\Facades\Storage;

class UserController extends Controller{

	function tap($id){
		$user = Auth::user();
		$userId = $user->id;
		$name = $user->first_name . ' '. $user->last_name;
		$userTap = UserFavorite::where('from_user_id', $userId)->where('to_user_id', $id)->first();
		$otherUserTap = UserFavorite::where('from_user_id', $id)->where('to_user_id', $userId)->first();

		//if the user did not add him/her to the favorites before
		if (empty($userTap)){
			//we to create a new record in the favorites table:
			$tap = UserFavorite::create([
				'from_user_id' => $userId,
				'to_user_id' => $id
			]);
			//we notify the other user that somebody tapped him/her
			$notify = 'Hey you! User '.$name.' tapped you';
			$notification = UserNotification::create([
				'user_id' => $id,
				'body' => $notify,
				'is_read' => 0,
			]);
			$message = 'Tapped successfully';
		}
		//if the user tapped th other user before: 
		else{
			$message = 'You already tapped this user before';
		}
		$usersConnection = UserConnection::where('user1_id', $userId)->where('user2_id', $id)->first();
		$otherUserConnection = UserConnection::where('user1_id', $id)->where('user2_id', $userId)->first();
		//if the other tapped the current user before we create a connection between them.
		if (!empty($otherUserTap)){
			if(empty($usersConnection) && empty($otherUserConnection)){
				$connection = UserConnection::create([
					'user1_id' => $userId,
					'user2_id' => $id
				]);
				$message2 = 'a new match';
				$notify1 = 'Hey you! You were matched with '.$name;
				$notification1 = UserNotification::create([
					'user_id' => $id,
					'body' => $notify1,
					'is_read' => 0,
				]);
				$otheruser = User::select('first_name','last_name')->where('id',$id)->first();
				$name2 = $otheruser->first_name .' '. $otheruser->last_name;
				$notify2 = 'Hey you! You were matched with '.$name2;
				$notification2 = UserNotification::create([
					'user_id' => $userId,
					'body' => $notify2,
					'is_read' => 0,
				]);
			}
		}
	}

	function block($id){
		$user = Auth::user();
		$userId = $user -> id;
		// to block users we need to check 3 main things if they are already blocked, have a connection or on the favorites list
		$blocked = UserBlocked::where('from_user_id',$userId)->where('to_user_id',$id)->first();
		if (empty($blocked)){
			//create a new block record
			$blockRec = UserBlocked::create([
				'from_user_id' => $userId,
				'to_user_id' => $id
			]);
			$message = 'Blocked successfully';
		}
	}
	
	function highlighted(){
		$user = Auth::user();
		$userId = $user->id;
		$interested_in = $user->interested_in;
		$blocked_users = $user->userblocked->pluck('to_user_id')->toArray();  // Pluck selects only the to_user_id column

		$highlighted_users = User::where("is_highlighted", 1)
									->where("gender", $interested_in)
									->whereNotIn("id", $blocked_users)->get()->toArray();
		return json_encode($highlighted_users);
	}
	
	function test(){
		$user = Auth::user();
		$id = $user->id;
		return json_encode(Auth::user());
	}

	function uploadPicture(Request $request) {
		$user = Auth::user();
		$id = $user->id;
		$validator = Validator::make($request->all(), [
            'picture' => 'required',  
			'is_profile_picture' => 'required'
        ]);
		
		if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->errors()
            ), 400);
        }

		// $image = base64_encode($request->file('image'));    used for testing
		
		$image = $request->picture;  
    	$image = str_replace('data:image/png;base64,', '', $image);
    	$image = str_replace(' ', '+', $image);
		$image = base64_decode($image);
        Storage::putFile('public', $image);

		// $file = $image->storeAs('users', 'public', $safeName); // stores the URL of the pic path

		$user_picture = UserPicture::create(
			// array_merge(
            // $validator->validated(),
			['user_id' => $id,
			'is_approved' => 0,
			'picture_url' => $file,
			'is_profile_picture' => $request->is_profile_picture]
		);

		return response()->json([
            'status' => true,
            'message' => 'Picture was successfully added',
        ], 201);
	}

	function updateProfile(Request $request) {
		$user = Auth::user();
		$id = $user->id;
		$validator = Validator::make($request->all(), [
            'first_name' => 'required|string|between:2,100',
			'last_name' => 'required|string|between:2,100',
            'gender' => 'required',
            'interested_in' => 'required',
			'dob' => 'required|date_format:Y-m-d|before:' . Carbon::now()->subYears(18)->format('Y-m-d'),
			'height' => 'integer',
			'weight' => 'integer',
			'nationality' => 'string|between:2,100',
			'net_worth' => 'integer',
			'currency' => 'string',
			'bio' => 'string|max:400'
        ]);

		if ($validator->fails()) {
            return response()->json(array(
                "status" => false,
                "errors" => $validator->errors()
            ), 400);
        }

		$update_profile = User::where('id',$id)->update(
            $validator->validated()
		);

		return response()->json([
            'status' => true,
            'message' => 'Profile was successfully updated',
        ], 201);
	}

	function search(Request $request) {
		$user = Auth::user();
		$userId = $user->id;
		$interest = $user->interested_in;
		$blocked_users = $user->userblocked->pluck('to_user_id')->toArray();
		$search_key = $request->keyword;
		$first_name = "";
		$last_name = "";
		$search_array = explode(" ", $search_key);
		//condition for if the search input was empty
		if(count($search_array) == 0) {    
			return;
			//condition for if the search input was only one word
		} elseif(count($search_array) == 1) {
			$first_name = $search_array[0];
			$obtained_users = User::where("first_name", 'LIKE', '%'.$first_name.'%')
			->orWhere("last_name", 'LIKE', '%'.$first_name.'%')
			->whereNotIn("id", $blocked_users)
			->where("gender", $interest)
			->where("id", "!=", $userId)->get()->toArray();
			
			//condition for if the search input was first name last name 
		} else {
			$first_name = $search_array[0];
			$last_name = $search_array[1];
			$obtained_users = User::where("first_name", 'LIKE', '%'.$first_name.'%')
			->where("last_name", 'LIKE', '%'.$last_name.'%')
			->whereNotIn("id", $blocked_users)
			->where("id", "!=", $userId)
			->where("gender", $interest)->get()->toArray();
		}
		return json_encode($obtained_users);
	}

	function getFeed() {
		$user = Auth::user();
		$userId = $user->id;
		$interest = $user->interested_in;
		$blocked_users = $user->userblocked->pluck('to_user_id')->toArray();
		$user_feed = User::whereNotIn("id", $blocked_users)
			->where("id", "!=", $userId)
			->where("gender", $interest)->get()->toArray();
		return json_encode($user_feed);
	}

	function getMessages() {
		$user = Auth::user();
		$userId = $user->id;
		$messages_data = UserMessage::where("reciever_id", $userId)->where("is_approved", 1)->get()->toArray();
		return json_encode($messages_data);
	}

	function sendMessages(Request $request) {
		$user = Auth::user();
		$userId = $user->id;
		$data = $request->all();
		$message = UserMessage::create([
			'sender_id' => $userId,
			'reciever_id' => $data->reciever_id,
			'body' => $data->body,
			'is_approved' => 0,
			'is_read' => 0
		]);

		return response()->json([
            'status' => true,
            'message' => 'Message successfully sent',
            'user' => $user
        ], 201);
	}
}

?>