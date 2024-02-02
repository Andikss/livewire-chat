<?php

namespace App\Livewire;

use App\Models\Chat\Conversation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Str;

class Users extends Component
{
    public $authenticatedUserId;

    public function mount()
    {
        $this->authenticatedUserId = Auth::user()->id;
    }

    public function message($userId)
    {
        $existingConversation = Conversation::where(function ($query) use ($userId) {
            $query->where('sender_id', $this->authenticatedUserId)->where('receiver_id', $userId);
        })->orWhere(function ($query) use ($userId) {
            $query->where('sender_id', $userId)->where('receiver_id', $this->authenticatedUserId);
        })->first();

        if ($existingConversation) {
            return redirect()->to("chat/{$existingConversation->id}");
        }        

        $createdConversation = Conversation::create([
            'id'          => Str::uuid(),
            'sender_id'   => $this->authenticatedUserId,
            'receiver_id' => $userId
        ]);

        if ($createdConversation) {
            return redirect()->to("chat/{$createdConversation->id}");
        }        
    }

    public function render()
    {
        return view('livewire.users', [
            'users' => User::whereNot('id', $this->authenticatedUserId)->get()
        ]);
    }
}
