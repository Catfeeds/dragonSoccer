<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Gteammembers extends Model{
	use SoftDeletes;
    protected $table='gteam_members';


    protected $fillable=['teamid','mid', 'name', 'isleader','position','positiont','isshowmsg','isshowname'];

   
    public function member(){
        return $this->belongsTo(Members::class,'mid');
    }

    public function team(){
        return $this->belongsTo(Gteam::class,'teamid');
    }
}