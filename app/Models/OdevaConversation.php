<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdevaConversation extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscriber()
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function messages()
    {
        return $this->hasMany(OdevaMessage::class, 'conversation_id');
    }
}
