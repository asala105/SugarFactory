<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request;

use App\Models\User;
use JWTAuth;
use Auth;


class AdminController extends Controller{
	
	function approve_pic(){
		return json_encode('approved pic');
	}
	function decline_pic(){
		return json_encode('declined pic');
	}
    function approve_message(){
		return json_encode('approved message');
	}
    function decline_message(){
		return json_encode('declined message');
	}
}

?>