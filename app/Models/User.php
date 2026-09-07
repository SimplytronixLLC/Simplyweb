<?php

namespace App\Models;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [ 'user_id','name','first_name','last_name','father_spouse','title','username', 'email', 'password', 'user_type','login_type','position_id','status', 'phone', 'photo','phone2','country_code','country_code2', 'email2', 'account_token','description'];
 
    protected $hidden = [ 'password', 'remember_token' ];
 
    protected $casts = ['email_verified_at' => 'datetime','email_verify'];
	
    public function isAdmin() {
       return $this->user_type === 'admin';
    }

    public function isUser() {
       return $this->user_type === 'user';
    } 

    public function isStaf() {
        return $this->user_type === 'staff';
     } 

    public function userDetails()
    {
        return $this->hasOne(UserDetails::class);
    }

    
	
}
