<?php

namespace App\Http\Livewire\Chat;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Str;
use App\Models\Chat\Message;

class ChatBox extends Component
{
    public $selectedConversation;
    public $paginate_var = 10;
    public $loadedMessages;
    public $body;

    protected $listeners = ['loadMore' => 'loadMore'];

    public function mount()
    {
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat.chat-box');
    }

    /**
     * ==============================================
     */

    public function loadMore(): void
    {
        $this->paginate_var += 10;
        $this->loadMessages();

        $this->dispatchBrowserEvent('update-chat-height');
    }

    public function loadMessages()
    {
        $count = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)->count();

        $this->loadedMessages = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)
            ->orderBy('created_at')
            ->skip($count - $this->paginate_var)
            ->take($this->paginate_var)
            ->get();

        return $this->loadedMessages;
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

        $this->dispatchBrowserEvent('scroll-bottom');
        $this->reset('body');

        $this->loadedMessages->push($message);
        $this->selectedConversation->updated_at = Carbon::now();
        $this->selectedConversation->save();

        $this->dispatchBrowserEvent('chat.chat-list', 'refresh');
    }
}
