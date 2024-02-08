<?php

use App\Http\Livewire\Users;
use App\Http\Livewire\Chat\Chat;
use App\Http\Livewire\Chat\Index;
use Illuminate\Support\Facades\Route;

Route::middleware('auth')->group(function () {
    Route::get('/chat', Index::class)->name('chat');
    Route::get('/chat/{query}', Chat::class)->name('chat.chat');
    Route::get('/users', Users::class)->name('user');
});
