<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Matchlogsetting extends Model{
	use SoftDeletes;
    protected $table='matchlog_setting';

    protected $fillable=['matchlogid','teamid', 'mid', 'mtime','status','rname','phone','address'];

    public function team(){
        return $this->hasOne(Team::class,'id','teamid');
    }

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
}