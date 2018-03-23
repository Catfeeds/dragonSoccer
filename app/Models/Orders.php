<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Orders extends Model{
	use SoftDeletes;
    protected $table='orders';

    protected $fillable=['sn','mid','gid','type','total','paytotal','paytime','number','payway','status'];

    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
    
    public function goods(){
        return $this->hasOne(Goods::class,'id','gid');
    }
    
}