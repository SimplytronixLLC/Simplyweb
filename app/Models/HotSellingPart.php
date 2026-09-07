<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotSellingPart extends Model
{
    protected $fillable = ['part_number', 'manufacturer', 'category', 'description', 'slug'];
}
