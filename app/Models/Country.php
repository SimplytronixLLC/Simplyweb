<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Country extends Model{
    public $table = "countries";
    protected $fillable = ['countries_id','countries_name','countries_iso_code_2','countries_iso_code_3','address_format_id' ];
}
