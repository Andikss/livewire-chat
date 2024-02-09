<?php

namespace App\Http\Livewire\Chat;

use Carbon\Carbon;
use Livewire\Component;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\Chat\Message;
use App\Notifications\MessageRead;
use App\Notifications\MessageSent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Collection;

class ChatBox extends Component
{
    public $selectedConversation;
    public $paginate_var = 10;
    public $loadedMessages;
    public $body;

    protected $listeners = ['loadMore' => 'loadMore'];

    public function getListeners(): array
    {
        $auth_id = Auth::user()->id;

        return [
            'loadMore',
            "echo-private:users.{$auth_id},.Illuminate\\Notifications\\Events\\BroadcastNotificationCreated" => 'broadcastedNotifications'
        ];
    }

    public function broadcastedNotifications($event)
    {
        if ($event['type'] == MessageSent::class) {
            if ($event['conversation_id'] == $this->selectedConversation->id) {
                $this->dispatchBrowserEvent('scroll-bottom');

                $message = Message::find($event['message_id']);
                $this->loadedMessages->push($message);

                $message->read_at = Carbon::now();
                $message->save();

                $this->selectedConversation->getReceiver()->notify(new MessageRead(
                    $this->selectedConversation->id
                ));

                
            }
        }
    }

    public function mount(): void
    {
        $this->loadMessages();
    }

    public function render(): View
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

    public function loadMessages(): Collection
    {
        $count = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)->count();

        $this->loadedMessages = Message::with(['sender'])->where('conversation_id', $this->selectedConversation->id)
            ->orderBy('created_at')
            ->skip($count - $this->paginate_var)
            ->take($this->paginate_var)
            ->get();

        return $this->loadedMessages;
    }

    public function sendMessage(): void
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

        // $this->emitTo('chat.chat-list', 'refresh');

        $this->selectedConversation->getReceiver()->notify(new MessageSent(
            Auth::user(),
            $message,
            $this->selectedConversation,
            $this->selectedConversation->getReceiver()->id
        ));
    }
}
