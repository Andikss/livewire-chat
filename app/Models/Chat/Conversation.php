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
}
