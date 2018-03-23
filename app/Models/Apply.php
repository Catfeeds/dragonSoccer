<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Apply extends Model{

    protected $table='apply';

    protected $fillable=['matchid','mid', 'position', 'positiont','friend_mid','status','province','city'];

    public function member(){
        return $this->belongsTo(Members::class,'mid');
    }

    public function match(){
        return $this->belongsTo(Match::class,'matchid');
    }

    public function friendmember(){
        return $this->belongsTo(Members::class,'friend_mid');
    }



}