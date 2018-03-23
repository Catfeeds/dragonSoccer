<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Gamesages extends Model{
    protected $table='games_ages';

    protected $fillable=['key', 'val','gamesid','starttime','endtime'];
    
    public function group(){
        return $this->hasMany('App\Models\Group','gamesagesid');
    }

    public function games(){
        return $this->belongsTo(Games::class,'gamesid');
    }
}