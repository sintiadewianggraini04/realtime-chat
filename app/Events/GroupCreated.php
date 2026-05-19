<?php

namespace App\Events;

use App\Models\Group;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function broadcastOn(): array
    {
        return [
            new Channel('groups'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'group.created';
    }
}