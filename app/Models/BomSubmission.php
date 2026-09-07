<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BomSubmission extends Model
{
    protected $fillable = [
        'bom_id',
        'name',
        'email',
        'phone',
        'company',
        'filename',
        'comments'
    ];
}