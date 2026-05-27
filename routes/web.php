<?php

use App\Http\Controllers\FollowController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\UserFollowListController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\UserSearchController;
use App\Http\Controllers\TweetController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/search', [UserSearchController::class, 'index'])->name('users.search');
Route::get('/users/{user}', [UserProfileController::class, 'show'])->name('users.show');
Route::get('/users/{user}/followers', [UserFollowListController::class, 'followers'])->name('users.followers');
Route::get('/users/{user}/following', [UserFollowListController::class, 'following'])->name('users.following');
Route::get('/tweets/{tweet}', [TweetController::class, 'show'])->name('tweets.show');

Route::get('/dashboard', [TweetController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::post('/users/{user}/follow', [FollowController::class, 'store'])->name('follows.store');
    Route::delete('/users/{user}/follow', [FollowController::class, 'destroy'])->name('follows.destroy');
    Route::post('/tweets/{tweet}/like', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/tweets/{tweet}/like', [LikeController::class, 'destroy'])->name('likes.destroy');
    Route::post('/tweets', [TweetController::class, 'store'])->name('tweets.store');
    Route::post('/tweets/{tweet}/replies', [TweetController::class, 'storeReply'])->name('tweets.replies.store');
    Route::delete('/tweets/{tweet}', [TweetController::class, 'destroy'])->name('tweets.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
