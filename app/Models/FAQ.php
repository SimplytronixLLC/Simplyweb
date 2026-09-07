<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FAQ extends Model{
    public $table = "faq";
    protected $fillable = ['id', 'type', 'question', 'answer', 'status', 'link', 'created_at', 'updated_at' ];
}
