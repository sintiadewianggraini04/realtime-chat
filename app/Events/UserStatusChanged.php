<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatusChanged implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $id;
    public $is_online;

    public function __construct(User $user)
    {
        $this->id = $user->id;
        $this->is_online = $user->is_online;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('online-status'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'user.status';
    }
}