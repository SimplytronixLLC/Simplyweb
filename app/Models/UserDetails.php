<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class UserDetails extends Model{
    public $table = "user_details";
    protected $fillable = ['id','user_id','journal_id','post_nature','post_title','recommended_name','recommended_email','position_id','priority','website','photo_id','about','editorial_note','editorial_subject','resume','consent','dob','gender','featured','move_to_editor','move_to_ear','join_date','leaving_date','membership_date','acknowledgement_mail']; 
    public $timestamps = false; 
} 
