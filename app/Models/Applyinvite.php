<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Applyinvite extends Model{

    protected $table='apply_invite';

    protected $fillable=['matchid','mid','friend_mid','status'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }

    public function match(){
        return $this->hasOne(Match::class,'id','matchid');
    }

    public function friendmember(){
        return $this->hasOne(Members::class,'id','friend_mid');
    }
}