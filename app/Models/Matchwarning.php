<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Matchwarning extends Model{
	
    protected $table='match_warning';
    protected $fillable=['matchid','mid','reason']; 

    public function match(){
        return $this->belongsTo('App\Models\Match','matchid');
    }

   
}