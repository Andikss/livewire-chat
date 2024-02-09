<?php

namespace App\Http\Livewire\Chat;

use App\Models\Chat\Conversation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ChatList extends Component
{

    public $selectedConversation;
    public $query;

    protected $listeners = ['refresh' => '$refresh'];

    public function deleteByUser($id)
    {
        $userId       = Auth::user()->id;
        $conversation = Conversation::find(decrypt($id));

        $conversation->messages()->each(function($message) use ($userId) {
            if($message->sender_id === $userId) {
                $message->update(['sender_deleted_at' => Carbon::now()]);
            } else if ($message->receiver_id === $userId) {
                $message->update(['receiver_deleted_at' => Carbon::now()]);
            }
        });

        $receiverAlsoDeleted = $conversation->messages()->where(function ($query) use ($userId) {
            $query->where('sender_id', $userId)->orWhere('receiver_id', $userId);
        })->where(function ($query) {
            $query->whereNull('sender_deleted_at')->orWhereNull('receiver_deleted_at');
        })->doesntExist();

        if($receiverAlsoDeleted) {
            $conversation->forceDelete();
        }

        return redirect(route('chat'));
    }

    public function render()
    {
        $user = User::find(Auth::user()->id);

        return view('livewire.chat.chat-list', [
            'conversations' => $user->conversations()->latest('updated_at')->get()
        ]);
    }
}
