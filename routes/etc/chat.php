<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Chat\Index;
use App\Livewire\Chat\Chat;
use App\Livewire\Users;

Route::middleware('auth')->group(function () {
    Route::get('/chat', Index::class)->name('chat');
    Route::get('/chat/{query}', Chat::class)->name('chat.chat');
    Route::get('/users', Users::class)->name('users');
});
