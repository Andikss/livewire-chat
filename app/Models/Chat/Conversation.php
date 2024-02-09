<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Conversation extends Model
{
    use HasFactory;

    protected $keyType    = 'string';
    public $incrementing  = false;
    protected $primaryKey = 'id';
    protected $table      = 'conversation';

    protected $fillable   = [
        'id',
        'receiver_id',
        'sender_id'
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'conversation_id', 'id');
    }

    public function getReceiver(): User
    {
        if ($this->sender_id === Auth::user()->id) {
            return User::firstWhere('id', $this->receiver_id);
        } else {
            return User::firstWhere('id', $this->sender_id);
        }
    }

    public function scopeWhereNotDeleted($query)
    {
        $userId = Auth::user()->id;

        return $query->where(function ($query) use ($userId) { 
            $query->whereHas('messages', function ($query) use ($userId) {
                $query->where(function ($query) use ($userId) {
                    $query->where('sender_id', $userId)->whereNull('sender_deleted_at');
                })->orWhere(function ($query) use ($userId) {
                    $query->where('receiver_id', $userId)->whereNull('receiver_deleted_at');
                });  
            })->orWhereDoesntHave('messages');
        });
    }

    public function isLastMessageReadByuser(): bool
    {
        $user = Auth::user();
        $lastMessage = $this->messages()->latest()->first();

        if ($lastMessage) {
            return !is_null($lastMessage->read_at) && $lastMessage->sender_id == $user->id;
        }
    }

    public function unreadMessagesCount(): int
    {
        return Message::where('conversation_id', $this->id)->where('receiver_id', Auth::user()->id)->whereNull('read_at')->count();
    }
}
