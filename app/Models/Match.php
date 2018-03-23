<?php

namespace App\Models;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class Match extends Model{

    protected $table='match';
    use SoftDeletes;
    protected $fillable=['name','rule','region','sex','level','applystarttime','applyendtime','starttime','endtime','imgs','status','sid','teamsts','remark']; 

    public function info(){
        return $this->hasOne(Matchinfo::class,'matchid');
    }

}
