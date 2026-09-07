<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $table = 'crm_contacts';

    protected $fillable = [
        'email',
        'name',
        'company',
        'phone',
        'source',
        'stage',
        'last_contact_date',
        'metadata',
        'notes',
        'bounced_at',
    ];

    protected $casts = [
        'metadata' => 'json',
        'last_contact_date' => 'datetime',
    ];
}
