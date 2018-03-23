<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Activityvote extends Model{

    protected $table='activity_vote';

    protected $fillable=['mid', 'bestmid','number', 'type'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }

    public function bestmember(){
        return $this->hasOne(Members::class,'id','bestmid');
    }
}