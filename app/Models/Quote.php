<?php 

    
namespace App\Models;


use Illuminate\Database\Eloquent\Model;

class Quote extends Model{ 

    public $table = "quote";
    protected $fillable = ['id','order_id','name', 'email', 'phone', 'company', 'part_number', 'quantity', 'comments','status','created_at','updated_at'];

}


?>