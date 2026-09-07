<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'cached_products';

    protected $fillable = [
    'product_key',
    'name',
    'description',
    'manufacturer',
    'category',
    'category_id',
    'quantity',
    'unit_price',
    'image'
];

    public $timestamps = false; // set true if your table has created_at / updated_at
}
