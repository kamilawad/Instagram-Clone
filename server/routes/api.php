<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\CommentController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::controller(UserController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('logout', 'logout');
    Route::post('refresh', 'refresh');
    Route::post('editProfile', 'editProfile');
    Route::get('getUserData', 'getUserData');
});

Route::controller(FollowerController::class)->group(function () {
    Route::post('follow', 'follow');
    Route::post('unfollow', 'unfollow');
    Route::get('getFollow', 'getFollow');
    Route::get('getAllUsers', 'getAllUsers');
});

Route::controller(PostController::class)->group(function () {
    Route::post('createPost', 'addPost');
    Route::get('getFeedPosts', 'getFeedPosts');
    Route::get('getUserPosts', 'getUserPosts');
});

Route::controller(LikeController::class)->group(function () {
    Route::post('likePost', 'likePost');
    Route::post('getPostLikes', 'getPostLikes');
});

Route::controller(CommentController::class)->group(function () {
    Route::post('addComment', 'addComment');
    Route::post('getPostComments', 'getPostComments');
});