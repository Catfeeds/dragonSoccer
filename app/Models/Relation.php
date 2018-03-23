<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Relation extends Model{

    protected $table='relation';

    protected $fillable=['mid','friend_mid','status'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
    
    public function friendmember(){
        return $this->hasOne(Members::class,'id','friend_mid');
    }
}