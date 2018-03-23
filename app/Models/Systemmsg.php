<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Systemmsg extends Model{

    protected $table='systemmsg';
    protected $fillable=['mid','content','type']; 

   
}
