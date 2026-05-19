<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    public function broadcastOn(): array
    {
        // GROUP
        if ($this->message->group_id) {

            return [
                new PrivateChannel(
                    'group.' . $this->message->group_id
                )
            ];
        }

        // PRIVATE
        $ids = [
            $this->message->sender_id,
            $this->message->receiver_id
        ];

        sort($ids);

        return [
            new PrivateChannel(
                'private.' . $ids[0] . '.' . $ids[1]
            )
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    public function broadcastWith(): array
    {
        return [
            'message' => [
                'id' => $this->message->id,
                'message' => $this->message->message,
                'sender_id' => $this->message->sender_id,
                'receiver_id' => $this->message->receiver_id,
                'group_id' => $this->message->group_id,
                'sender' => [
                    'id' => $this->message->sender->id,
                    'name' => $this->message->sender->name,
                ]
            ]
        ];
    }
}