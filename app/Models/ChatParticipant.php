<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatParticipant extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'chat_id',

    ];
    /**
     * Get the chat that owns the ChatParticipant
     *
     * @return \Illuminate\DatabChatEloquent\Relations\BelongsTo
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class, 'chat_id');
    }
    /**
     * Get the user that owns the ChatParticipant
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
