<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Gteaminvite extends Model{

    protected $table='gteam_invite';

    protected $fillable=['gteamid','mid','fmid','status'];

    public function gteam(){
        return $this->belongsTo(Gteam::class,'gteamid');
    }

    public function members(){
        return $this->belongsTo(Members::class,'mid');
    }

    public function fmembers(){
        return $this->belongsTo(Members::class,'fmid');
    }
}