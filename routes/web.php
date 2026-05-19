<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;


// ROOT
Route::get('/', function () {

    if(auth()->check())
    {
        return redirect('/chat');
    }

    return redirect('/login');

});

// AUTH CHAT
Route::middleware('auth')->group(function () {

    // CHAT
    Route::get('/chat', [ChatController::class, 'index'])
        ->name('chat');

    // SEND MESSAGE
    Route::post('/send-message', [ChatController::class, 'sendMessage'])
        ->name('send.message');

    // EDIT MESSAGE
    Route::put('/message/{id}', [ChatController::class, 'update'])
        ->name('message.update');

    // DELETE MESSAGE
    Route::delete('/message/{id}', [ChatController::class, 'deleteMessage'])
        ->name('message.delete');

    // GROUP CHAT
    Route::get('/group/{id}', [ChatController::class, 'groupChat'])
        ->name('group.chat');

    // CREATE GROUP
    Route::post('/group/create', [ChatController::class, 'createGroup'])
        ->name('group.create');

    // PROFILE
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');

    Route::get('/chat/user/{id}', [ChatController::class, 'privateChat'])
       ->name('private.chat');

    Route::post('/group/{id}/add-member', [ChatController::class, 'addMember'])
        ->name('group.add.member');

    Route::delete('/group/{group}/remove-member/{user}',[ChatController::class, 'removeMember'])
        ->name('group.remove.member');

    
});

require __DIR__.'/auth.php';