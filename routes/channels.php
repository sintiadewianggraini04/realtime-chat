<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel(
    'private.{id1}.{id2}',
    function ($user, $id1, $id2) {

        return in_array(
            $user->id,
            [(int)$id1, (int)$id2]
        );
    }
);

Broadcast::channel(
    'group.{groupId}',
    function ($user, $groupId) {

        return true;
    }
);

Broadcast::channel(
    'online-status',
    function ($user) {

        return true;
    }
);

Broadcast::channel('user.{id}.groups', function ($user, $id) {
    return (int) $user->id === (int) $id;
});