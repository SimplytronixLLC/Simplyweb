<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserAddress extends Model{
    public $table = "user_address";
    protected $fillable = ['id','user_id','type','personal_phone','home_phone','email','institution','designation','deparment','address','city','state','country','pincode','institinal_mail','created_at','updated_at']; 
    public $timestamps = false;
} 
