<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class LoginHistory extends Model{
    public $table = "login_history";
    protected $fillable = ['id','user_id','user_name','user_type','ip_address','browser','device_type','last_login','current_login','status','city','state','country','created_at','updated_at' ]; 
}
