<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Team extends Model{
	use SoftDeletes;
    protected $table='team';

    protected $fillable=['matchid','icon', 'name', 'type','ischange','province','city'];

    public function match(){
        return $this->hasOne(Match::class,'id','matchid');
    }

    public function teammember(){
        return $this->hasMany(Teammember::class,'teamid');
    }

    
}