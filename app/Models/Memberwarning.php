<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Memberwarning extends Model{
	
    protected $table='member_warning';
    protected $fillable=['memberid','mid','reason']; 

    public function match(){
        return $this->belongsTo('App\Models\Match','matchid');
    }

   
}