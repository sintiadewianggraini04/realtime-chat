<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'sender_id',
        'receiver_id',
        'group_id',
        'message'
    ];

    // SENDER
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // RECEIVER
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // GROUP
    public function group()
    {
        return $this->belongsTo(Group::class);
    }
}