<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Gamesruler extends Model{
    protected $table='games_ruler';

    protected $fillable=['id','key', 'gamesid','teamnumber','risenumber','additionalnumber','starttime','endtime'];
    
}