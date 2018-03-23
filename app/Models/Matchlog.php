<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Matchlog extends Model{
	use SoftDeletes;
    protected $table='match_log';

    protected $fillable=['matchid','groupsn', 'ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','province','city','stime','successteamid','failedteamid','address'];

    public function match(){
        return $this->hasOne(Match::class,'id','matchid');
    }
    
    public function ateam(){
        return $this->hasOne(Team::class,'id','ateamid');
    }
    
    public function bteam(){
        return $this->hasOne(Team::class,'id','bteamid');
    }
}