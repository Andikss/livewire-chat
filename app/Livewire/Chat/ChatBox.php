<?php

namespace App\Livewire\Chat;

use Illuminate\Support\Str;
use App\Models\Chat\Message;
use Livewire\Component;

class ChatBox extends Component
{

    public $selectedConversation;
    public $loadedMessages;
    public $body;

    public function render()
    {
        return view('livewire.chat.chat-box');
    }

    public function mount()
    {
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $this->loadedMessages = Message::where('conversation_id', $this->selectedConversation->id)->get();
    }

    public function sendMessage()
    {
        $this->validate([ 'body' => 'required|string' ]);
        
        $message = Message::create([
            'id'              => Str::uuid(),
            'conversation_id' => $this->selectedConversation->id,
            'sender_id'       => auth()->id(),
            'receiver_id'     => $this->selectedConversation->getReceiver()->id,
            'body'            => $this->body,
        ]);

        $this->loadedMessages->push($message);

        $this->body = '';
    }
}
