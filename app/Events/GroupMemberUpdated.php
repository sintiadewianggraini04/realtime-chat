<?php

namespace App\Events;

use App\Models\Group;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GroupMemberUpdated implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $group;
    public $user;
    public $action;

    public function __construct(Group $group, User $user, string $action)
    {
        $this->group = $group;
        $this->user = $user;
        $this->action = $action;
    }

    public function broadcastOn()
    {
        return [
            new Channel('user.' . $this->user->id . '.groups'),
            new PrivateChannel('group.' . $this->group->id),
        ];
    }

    public function broadcastAs()
    {
        return 'group.member.updated';
    }

    public function broadcastWith()
    {
        return [
            'action' => $this->action,

            'group' => [
                'id' => $this->group->id,
                'name' => $this->group->name,
            ],

            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ],
        ];
    }
}