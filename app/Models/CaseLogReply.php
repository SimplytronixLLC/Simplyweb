<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CaseLogReply extends Model{
    public $table = "case_log_reply";
    protected $fillable = ['id','case_id','sender_id','status','comment','attachments','created_at']; 
    public $timestamps = false;
} 
