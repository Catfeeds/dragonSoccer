<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use SoftDeletes;
    protected $table='notice';
    protected $fillable=['title','content','status','rsort']; 
    
}
