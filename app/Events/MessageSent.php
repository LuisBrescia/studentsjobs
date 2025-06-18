<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct($message)
    {
        $this->message = $message->loadMissing(['sender', 'receiver']);
    }

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat.' . $this->message->chat_session_id),
        ];
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'chat_session_id' => $this->message->chat_session_id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'content' => $this->message->content,
            'created_at' => $this->message->created_at->toDateTimeString(),
            'sender' => [
                'id' => $this->message->sender->id,
                'nome' => $this->message->sender->nome,
            ]
        ];
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}
