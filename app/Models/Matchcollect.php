<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Matchcollect extends Model{
	
    protected $table='match_collect';
    protected $fillable=['matchid','mid']; 
    
    public function match(){
        return $this->belongsTo('App\Models\Match','matchid');
    }

    public function games(){
        return $this->belongsTo('App\Models\Games','matchid');
    }
}