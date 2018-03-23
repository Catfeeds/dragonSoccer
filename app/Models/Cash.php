<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;	
class Cash extends Model{
	use SoftDeletes;
    protected $table='cash';
    protected $fillable=['name','icon','type','mid','money','remark']; 

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }

}