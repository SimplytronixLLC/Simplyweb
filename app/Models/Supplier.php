<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Supplier extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'company_name',
        'contact_name',
        'email',
        'phone',
        'country',
        'password',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token'
    ];
}