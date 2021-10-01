<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserFavorite;
use App\Models\UserNotification;
use App\Models\UserConnection;
use App\Models\UserBlocked;
use Auth;

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
		$highlighted_users = User::where("is_highlighted", 1)->limit(6)->get()->toArray();
		return json_encode($highlighted_users);
	}
	
	function test(){
		$user = Auth::user();
		$id = $user->id;
		return json_encode(Auth::user());
	}

}

?>