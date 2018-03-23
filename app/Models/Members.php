<?php
namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Database\Eloquent\SoftDeletes;

class Members extends Authenticatable{

    protected $table='members';
    use SoftDeletes;
    protected $fillable=['icon','name', 'password','birthday','mobile','sex','idnumber','province','city','country','address','school','position','foot','status','instruction','height','weight','isshow','idcard_b','idcard_f','idcard_address','img','truename','nation','recommend','ali','wechat'];

    protected $hidden = [
        'password', 'remember_token',
    ];

    public function apply(){
        return $this->hasMany(Apply::class,'mid');
    }


    public function collect(){
        return $this->hasMany('App\Models\Matchcollect','mid');
    }

    public function warning(){
        return $this->hasMany('App\Models\Matchwarning','mid');
    }

    public function groupmembers(){
        return $this->hasMany('App\Models\Groupmembers','mid');
    }


}
