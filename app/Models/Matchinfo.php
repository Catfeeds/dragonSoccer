<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Matchinfo extends Model{
	
    protected $table='match_info';
    use SoftDeletes;
    protected $fillable=['matchid','content']; 
    
}