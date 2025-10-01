<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OdevaSubscription extends Model
{
    protected $guarded = [];

    protected $casts = [
        'settings' => 'array',
        'trial_ends_at' => 'date',
        'next_billing_date' => 'date',
        'automation_enabled' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function isActive()
    {
        return in_array($this->status, ['trial', 'active']);
    }

    public function isOnTrial()
    {
        return $this->status === 'trial' && $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }
}
