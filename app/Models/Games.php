<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Games extends Model{
	use SoftDeletes;
    protected $table='games';

    protected $fillable=['name', 'sid', 'info','applystime','applyetime','starttime','endtime','ruler','owner','imgs','status'];
    
    public function ages(){
        return $this->hasMany('App\Models\Gamesages','gamesid');
    }

    public function content(){
        return $this->hasMany('App\Models\Gamecontent','gamesid');
    }

    public function school(){
        return $this->belongsTo('App\Models\School','owner');
    }
}