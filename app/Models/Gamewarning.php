<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Gamewarning extends Model{
	
    protected $table='game_warning';
    protected $fillable=['gameid','mid','reason']; 

    public function game(){
        return $this->belongsTo('App\Models\Games','gameid');
    }

   
}