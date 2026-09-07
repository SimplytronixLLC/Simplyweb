<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CachedProduct extends Model
{
    protected $fillable = [
        'product_key',
        'manufacturer',
        'name',
        'description',
        'category',
        'category_id',
        'image',
        'quantity',
        'raw_data',
        'unit_price',
        'is_synced',
        'specs',
        'specs_synced'
    ];

    protected $casts = [
        'raw_data' => 'array',
        'specs' => 'array',
        'specs_synced' => 'boolean',
    ];
    
    public function categoryRelation()
    {
        return $this->belongsTo(\App\Models\Category::class, 'category_id');
    }
}