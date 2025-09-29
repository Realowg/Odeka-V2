<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserImport extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id','filename','storage_path','options','total_rows','created_count','updated_count','skipped_count','failed_count','status','errors_csv_path','summary_json_path','started_at','finished_at'
    ];

    protected $casts = [
        'options' => 'array',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}


