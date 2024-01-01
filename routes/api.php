<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommentController;
use App\Http\Controllers\Api\FriendshipController;
use App\Http\Controllers\Api\LikeController;
use App\Http\Controllers\Api\NewsFeedController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\SocialiteController;
use Illuminate\Support\Facades\Route;

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


Route::prefix('auth')->group(function (){
    Route::controller(AuthController::class)->group(
        function () {
            Route::post('/login','login');
            Route::post('/register',  'register');
            Route::post('/logout',  'logout');
            Route::post('/refresh', 'refresh');
            Route::post('/forgot-password', 'forgotPassword');
            Route::post('/reset-password', 'resetPassword');
        });

});

Route::prefix('profile')->middleware('auth:api')->group(function(){
    Route::controller(ProfileController::class)->group(
        function () {
            Route::get('/show','show');
            Route::post('/update',  'update');

        });
});

Route::middleware('auth:api')->prefix('/friendship')->controller(FriendshipController::class)->group(function () {
    // Send friend request
    Route::post('/send-request/{friend}',  'sendFriendRequest');

    // Accept friend request
    Route::put('/accept-request/{friendshipId}', 'acceptFriendRequest');

    // Reject friend request
    Route::delete('/reject-request/{friendshipId}',  'rejectFriendRequest');

    Route::get('/requests/received',  'getFriendRequestsReceived');

    // Get user's friends
    Route::get('/friends','getFriendList');

});

Route::prefix('post')->group(function (){
    Route::controller(PostController::class)->group(function (){
        Route::post('/add','store');
        Route::get('/show','show');
        Route::post('/update/{post}','update')->middleware('ownership:post');
        Route::delete('/delete/{post}','delete')->middleware('ownership:post');
    });

    Route::post('/{post}/share', [NewsFeedController::class, 'sharePost']);

    Route::controller(LikeController::class)->group(function () {
        Route::post('/like/{post}', 'likePost');
        Route::delete('/unlike/{post}', 'unLikePost');
    });

    Route::prefix('/comment')->controller(CommentController::class)->group(function () {
        Route::post('/add/{post}', 'store');
        Route::put('/update/{post}','update');
        Route::delete('/delete/{post}', 'destroy');
    });

})->middleware('auth:api');

Route::middleware('auth:api')->prefix('/news-feed')->group(function () {
    Route::get('/', [NewsFeedController::class,'getNewsFeed']);

});

Route::prefix('auth/github')->controller(SocialiteController::class)->group(function(){
   Route::get('/login','login');
    Route::get('/redirect','redirect');

});
