<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApiUsage extends Model
{
   protected $fillable = [
    'provider',
    'called_at',
    'endpoint',
    'query',
    'controller',
    'ip_address',
    'user_agent',
    'key_index',
    'status_code',
    'retry_after_seconds',
];
    protected $casts = [
    'called_at' => 'datetime',
    ];
}
