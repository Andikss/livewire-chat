<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $table      = 'messages';

    protected $fillable   = [
        'id',
        'body',
        'sender_id',
        'receiver_id',
        'conversation_id',
        'read_at',
        'receiver_deleted_at',
        'sender_deleted_at'
    ];

    protected $dates = ['read_at', 'receiver_deleted_at', 'sender_deleted_at'];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class, 'id', 'conversation_id');
    }

    public function isRead(): bool
    {
        return $this->read_at != null;
    }
}
