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
Route::post('/login', [AuthController::class, 'login'])->name('api:login');

Route::group(['middleware' => 'auth.jwt'], function () {
	Route::group(['middleware' => ['admin']], function () {
		Route::get('/approve_pic', [AdminController::class, 'approve_pic'])->name('api:approve_pic');
		Route::get('/decline_pic', [AdminController::class, 'decline_pic'])->name('api:decline_pic');
		Route::get('/approve_message', [AdminController::class, 'approve_message'])->name('api:approve_message');
		Route::get('/decline_message', [AdminController::class, 'decline_message'])->name('api:decline_message');
	});
	Route::get('/search/{keyword}', [UserController::class, 'search'])->name('api:search');
	Route::get('/test', [UserController::class, 'test'])->name('api:test');
	Route::post('logout', [AuthController::class, 'logout'])->name('api:logout');
});
