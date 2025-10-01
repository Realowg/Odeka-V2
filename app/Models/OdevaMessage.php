<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdevaMessage extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function conversation()
    {
        return $this->belongsTo(OdevaConversation::class, 'conversation_id');
    }
}
