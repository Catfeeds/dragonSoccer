<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gteam extends Model{
	use SoftDeletes;
    protected $table='gteam';

    protected $fillable=['gamesagesid','icon', 'name', 'type','deletemid','province','city','gid','status'];
    
    public function teammember(){
        return $this->hasMany(Gteammembers::class,'teamid');
    }

    public function gamesages(){
        return $this->belongsTo(Gamesages::class,'gamesagesid');
    }
}