<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdevaUsageLog extends Model
{
    protected $fillable = [
        'creator_id',
        'subscriber_id',
        'conversation_id',
        'action',
        'tokens_used',
        'cost',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'cost' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(User::class, 'subscriber_id');
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(OdevaConversation::class, 'conversation_id');
    }
}

