<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\AdminController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/highlighted', [UserController::class, 'highlighted'])->name('api:highlighted');
//Route::post('/login', [AuthController::class, 'login'])->name('api:login');   <-- Charbel's
Route::post('/login', [AuthController::class, 'login'])->name('api:login');                    // <--
Route::post('/register', [AuthController::class, 'register'])->name('api:register');			  // Api routes added by Mike
Route::post('/refresh', [AuthController::class, 'refresh'])->name('api:refresh');
 //
Route::group(['middleware' => 'auth.jwt'], function () {   

	Route::group(['middleware' => ['admin']], function () {
		
		Route::get('/view_all_users', [AdminController::class, 'view_all_users'])->name('api:view_users');
		Route::get('/view_all_pics', [AdminController::class, 'view_all_pics'])->name('api:view_pics');
		Route::get('/view_all_messages', [AdminController::class, 'view_all_messages'])->name('api:view_messages');
		
		Route::put('/highlight_user/{id}', [AdminController::class, 'highlight_user'])->name('api:highlight_user');
		Route::put('/remove_highlight/{id}', [AdminController::class, 'remove_highlight'])->name('api:remove_highlight');
		Route::put('/approve_pic/{id}', [AdminController::class, 'approve_pic'])->name('api:approve_pic');
		Route::put('/approve_message/{id}', [AdminController::class, 'approve_message'])->name('api:approve_message');

		Route::delete('/decline_pic/{id}', [AdminController::class, 'decline_pic'])->name('api:decline_pic');
		Route::delete('/decline_message/{id}', [AdminController::class, 'decline_message'])->name('api:decline_message');
	});
	
	Route::get('/user_profile', [AuthController::class, 'userProfile'])->name('api:user_profile');
	Route::post('/tap/{id}', [UserController::class, 'tap'])->name('api:tap');
	Route::post('/block/{id}', [UserController::class, 'block'])->name('api:block');
	Route::get('/search/{keyword}', [UserController::class, 'search'])->name('api:search');
	Route::get('/test', [UserController::class, 'test'])->name('api:test');
	Route::post('/logout', [AuthController::class, 'logout'])->name('api:logout');   
});
