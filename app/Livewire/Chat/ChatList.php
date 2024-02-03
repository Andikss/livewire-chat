<?php

namespace App\Livewire\Chat;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{

    public $selectedConversation;
    public $query;

    public function render()
    {
        $user = User::find(Auth::user()->id);

        return view('livewire.chat.chat-list', [
            'conversations' => $user->conversations()->latest('updated_at')->get()
        ]);
    }
}
