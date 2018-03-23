<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Gamecollect extends Model{
	
    protected $table='game_collect';
    protected $fillable=['gameid','mid']; 
    
    public function game(){
        return $this->belongsTo('App\Models\Games','gameid');
    }
}