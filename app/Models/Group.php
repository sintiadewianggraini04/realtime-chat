<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name'
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function users()
{
    return $this->belongsToMany(User::class);
}
    
}