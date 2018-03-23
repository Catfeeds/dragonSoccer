<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Matchlogcontent extends Model{
	use SoftDeletes;
    protected $table='matchlog_content';

    protected $fillable=['matchlogid','teamid', 'mid', 'type','txt1','txt2','imgs'];

    public function match(){
        return $this->hasOne(Match::class,'id','matchid');
    }
    
    public function team(){
        return $this->hasOne(Team::class,'id','teamid');
    }

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
}