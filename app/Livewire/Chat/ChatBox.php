<?php

namespace App\Livewire\Chat;

use Illuminate\Support\Str;
use App\Models\Chat\Message;
use Carbon\Carbon;
use Livewire\Component;

class ChatBox extends Component
{

    protected $listeners = ['refreshChatList' => 'refreshList'];
    public $selectedConversation;
    public $loadedMessages;
    public $body;

    public $paginate_var = 10;

    public function load(): void
    {
        $this->paginate_var += 10;

        $this->loadMessages();
    }


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
        $count = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)->count();

        $this->loadedMessages = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)
            ->skip($count - $this->paginate_var)
            ->take($this->paginate_var)
            ->get();
    }

    public function sendMessage()
    {
        $this->validate(
            ['body'          => 'required|string'],
            ['body.required' => 'Message cannot be empty!']
        );

        $message = Message::create([
            'id'              => Str::uuid(),
            'conversation_id' => $this->selectedConversation->id,
            'sender_id'       => auth()->id(),
            'receiver_id'     => $this->selectedConversation->getReceiver()->id,
            'body'            => $this->body,
        ]);

        $this->dispatch('scroll-bottom');
        // $this->dispatch('messageSent');

        $this->reset('body');

        $this->loadedMessages->push($message);

        $this->selectedConversation->updated_at = Carbon::now();
        $this->selectedConversation->save();

        $this->dispatch('chat.chat-list', 'refresh');
    }

    public function refreshList()
    {
        $this->refresh();
    }
}
