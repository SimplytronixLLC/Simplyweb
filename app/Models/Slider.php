<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Slider extends Model{
    public $table = "slider";
    protected $fillable = ['id','name','description','page_link','image','status','created_at','updated_at']; 
}