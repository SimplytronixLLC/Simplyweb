<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Manufacturers extends Model{
    public $table = "manufacturers";
    protected $fillable = ['id','m_id','name','status','created_at','updated_at'];
}
