<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'title',
        'type',
        'session_id',
    ];

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }
}