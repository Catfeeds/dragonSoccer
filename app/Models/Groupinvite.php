<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Groupinvite extends Model{

    protected $table='group_invite';

    protected $fillable=['groupid','mid','fmid','status'];

    public function group(){
        return $this->belongsTo(Group::class,'groupid');
    }

    public function members(){
        return $this->belongsTo(Members::class,'mid');
    }

    public function fmembers(){
        return $this->belongsTo(Members::class,'fmid');
    }
}