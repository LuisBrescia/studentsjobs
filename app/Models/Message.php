<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{

    protected $fillable = [
        'chat_session_id',
        'sender_id',
        'content',
        'receiver_id'
    ];

    public function chatSession()
    {
        return $this->belongsTo(ChatSession::class, 'chat_session_id');
    }

    public function sender()
    {
        return $this->belongsTo(Usuario::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(Usuario::class, 'receiver_id');
    }
}
