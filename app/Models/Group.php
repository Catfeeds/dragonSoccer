<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Group extends Model{
	use SoftDeletes;
    protected $table='group';
    
    protected $fillable=['gamesagesid','number','province','city', 'status','type'];

    public function gamesages(){
        return $this->belongsTo(Gamesages::class,'gamesagesid');
    }

    public function gmember(){
        return $this->hasMany(Groupmembers::class,'groupid');
    }
}