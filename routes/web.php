<?php

use App\Http\Controllers\Admin\GameManagementController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\GameController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

// Welcome & Dashboard
Route::get('/', function () {
    return redirect()->route('games.index');
})->name('home');

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

// Public Browsing (No auth required - Reddit & Fandom Style!)
Route::get('/games', [GameController::class, 'index'])->name('games.index');
Route::get('/games/{game:slug}', [GameController::class, 'show'])->name('games.show');
Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
Route::get('/users/{username}', [\App\Http\Controllers\UserProfileController::class, 'show'])->name('profile.show');

// Authenticated Interaction (Requires login)
Route::middleware('auth')->group(function () {
    // Profile settings
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Posts & Comments CRUD
    Route::resource('posts', PostController::class)->except(['index', 'show']);
    Route::post('/posts/{post}/solve', [PostController::class, 'markSolved'])->name('posts.solve');
    Route::resource('comments', CommentController::class)->only(['store', 'update', 'destroy']);

    // Follow toggles (AJAX endpoints)
    Route::post('/ajax/follow/game', [\App\Http\Controllers\FollowController::class, 'toggleGame'])->name('follow.game');
    Route::post('/ajax/follow/user', [\App\Http\Controllers\FollowController::class, 'toggleUser'])->name('follow.user');
    Route::post('/ajax/vote', [\App\Http\Controllers\VoteController::class, 'toggleAjax'])->name('vote.toggle');

    // Notifications channels
    Route::get('/notifications', [\App\Http\Controllers\NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/ajax/notifications', [\App\Http\Controllers\NotificationController::class, 'indexJson'])->name('notifications.ajax');
    Route::post('/ajax/notifications/{notification}/read', [\App\Http\Controllers\NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [\App\Http\Controllers\NotificationController::class, 'markAllRead'])->name('notifications.read-all');
});

// Admin-Only Game Curator Panel (Requires admin role)
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('games/lookup', [GameManagementController::class, 'lookup'])->name('games.lookup');
    Route::resource('games', GameManagementController::class);
});

require __DIR__.'/auth.php';
