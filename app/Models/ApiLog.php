<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiLog extends Model
{
    protected $guarded = [];

    public $timestamps = false;

    protected $casts = [
        'request_headers' => 'array',
        'request_body' => 'array',
        'response_headers' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
