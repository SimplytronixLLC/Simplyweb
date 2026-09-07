<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CachedProduct;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'parent_id',
        'level'
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // Parent category
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Child categories
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    // Recursive children (for 3-level loading)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }

    // Products (we will connect this later)
    public function products()
    {
        return $this->hasMany(CachedProduct::class, 'category_id');
    }
    
}