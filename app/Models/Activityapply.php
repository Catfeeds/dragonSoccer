<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Activityapply extends Model{

    protected $table='activity_apply';

    protected $fillable=['mid', 'txt', 'status','remark','imgs'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
}