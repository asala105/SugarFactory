<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Models\User;
use JWTAuth;
use Auth;
use App\Models\UserPicture;
use App\Models\UserMessage;
class AdminController extends Controller{
	function view_all_users(){
		//may add filter i.e search
		$users = User::all();
		return json_encode($users);
	}
	function view_all_pics(){
		//may add filter i.e search
		$users_pics = UserPicture::all();
		return json_encode($users_pics);
	}
	function view_all_messages(){
		//may add filter i.e search
		$users_messages = UserMessage::all();
		return json_encode($users_messages);
	}
	function remove_highlight($id){
		//basically we change the is_highlighted attribute to 1
		$user = User::where('id',$id)->update(['is_highlighted'=>0]);
		return json_encode('highlight removed');
	}
	function highlight_user($id){
		//basically we change the is_highlighted attribute to 1
		$user = User::where('id',$id)->update(['is_highlighted'=>1]);
		return json_encode('highlighted');
	}
	function approve_pic($id){
		//basically we change the is_approved attribute to 1
		$pic = UserPicture::where('id',$id)->update(['is_approved'=>1]);
		return json_encode('approved');
	}
	function decline_pic($id){
		//here when the admin declines the pic it will be deleted and the user will be notified
		$pic = UserPicture::where('id',$id)->delete();
		return json_encode('declined pic');
	}
    function approve_message($id){
		$pic = UserMessage::where('id',$id)->update(['is_approved'=>1]);
		return json_encode('approved message');
	}
    function decline_message(){
		return json_encode('declined message');
	}
}

?>