<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Gamecontent extends Model{
    protected $table='game_content';

    protected $fillable=['gamesid','sid','img','txt'];

    public function games(){
        return $this->belongsTo(Games::class,'gamesid');
    }
}