<?php

namespace App\Http\Livewire\Chat;

use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Chat extends Component
{
    public $query;
    public $selectedConversation;

    public function mount()
    {
        $this->selectedConversation = Conversation::findOrFail($this->query);

        Message::where('conversation_id', $this->selectedConversation->id)
            ->where('receiver_id', Auth::user()->id)->whereNull('read_at')
            ->update(['read_at' => Carbon::now()]);
    }

    public function render()
    {
        return view('livewire.chat.chat');
    }
}
