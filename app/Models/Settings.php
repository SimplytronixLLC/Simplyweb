<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Settings extends Model{
    public $table = "settings";
    protected $fillable = ['logo', 'favicon', 'title', 'url', 'about', 'address', 'phone', 'email' ];
    public $timestamps = false; 
}
