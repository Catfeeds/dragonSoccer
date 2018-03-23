<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Groupmembers extends Model{
	use SoftDeletes;
    protected $table='group_members';
    
    protected $fillable=['groupid','mid','isleader','position', 'positiont'];

    public function members(){
        return $this->belongsTo(Members::class,'mid');
    }

    public function group(){
        return $this->belongsTo(Group::class,'groupid');
    }
}