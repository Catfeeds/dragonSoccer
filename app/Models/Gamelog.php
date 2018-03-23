<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gamelog extends Model{
	use SoftDeletes;
    protected $table='game_log';

    protected $fillable=['gamesagesid','groupsn', 'ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','province','city','stime','successteamid','failedteamid','address'];

    public function gamesages(){
        return $this->belongsTo(Gamesages::class,'gamesagesid');
    }
    
    public function ateam(){
        return $this->belongsTo(Team::class,'ateamid');
    }
    
    public function bteam(){
        return $this->belongsTo(Team::class,'bteamid');
    }
}