<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OdevaCostAnalytics extends Model
{
    protected $fillable = [
        'date',
        'creator_id',
        'total_requests',
        'total_tokens',
        'total_cost',
        'breakdown',
    ];

    protected $casts = [
        'date' => 'date',
        'breakdown' => 'array',
        'total_cost' => 'decimal:6',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}

