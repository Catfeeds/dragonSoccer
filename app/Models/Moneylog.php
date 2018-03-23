<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Moneylog extends Model{
    protected $table='money_log';
    protected $fillable=['sn','mid','type','money'];
    
    public function member(){
        return $this->hasOne(Members::class,'id','mid');
    }
}