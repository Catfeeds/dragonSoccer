<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Teammember extends Model{

    protected $table='team_member';


    protected $fillable=['teamid','mid', 'name', 'isleader','position','positiont','isshowmsg','isshowname'];

   
    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }

    public function team(){
        return $this->belongsTo('App\Models\Team','teamid');
    }
}