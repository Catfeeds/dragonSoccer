<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Gamesrulerinfo extends Model{
    protected $table='games_ruler_info';
    protected $fillable=['gamesrulerid','key','starttime','endtime'];
    
    public function ruler(){
        return $this->belongsTo(Gamesruler::class,'gamesrulerid');
    }
}