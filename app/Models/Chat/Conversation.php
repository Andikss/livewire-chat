<?php

namespace App\Models\Chat;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
