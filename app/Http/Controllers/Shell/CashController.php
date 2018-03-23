<?php
namespace App\Http\Controllers\Shell;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;


use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Cash;
use Illuminate\Support\Facades\Redis;
use DB;
use Config;
class CashController extends Controller
{
    public $cashArr = array();

    public function __construct(){
        $this->cashArr = Config::get('custom.cash');
    }


    //从apply中查找符合条件的mid  插入cash中
    public function applyToCash(Request $request){
        $id = Redis::get('applyToCashid');
        var_dump($id);
        $applyArr = Groupmembers::whereHas('group',function($q){ $q->where('status','>=','2');})->where('id','>',empty($id)?0:$id )->limit(30)->get();
        //var_dump($applyArr->toArray());
        if(!empty($applyArr->toArray())){
            foreach ($applyArr as $v) {
                if($r = Cash::where('mid','=',$v->mid)->withTrashed()->first()){
                    if(!empty($r->deleted_at)){
                        Cash::where('id','=',$r->id)->withTrashed()->update(array('deleted_at'=>null));  
                        echo "update mid:$v->mid id:$v->id\r\n";  
                    }
                }else{
                    Cash::create(array('mid'=>$v->mid,'money'=>$this->cashArr['moneyArr']['joinmatch'],'type'=>'apply')); 
                    echo "insert mid:$v->mid id:$v->id\r\n";
                }
                Redis::set('applyToCashid',$v->id);
            }
        }else{
            Redis::set('applyToCashid',0);
        }

        
    }

    //从cash中获取mid  如果不在apply中 删除
    public function cashToApply(Request $request){
        $id = Redis::get('cashToApplyid');
        //var_dump($id);
        $cashArr = Cash::where('type','=','apply')->where('id','>',empty($id)?0:$id )->limit(20)->get(['mid','id']);
        //var_dump($cashArr->toArray());
        if(!empty($cashArr->toArray())){
            foreach ($cashArr as $v) {
                //var_dump($v->mid);
                $gr = Groupmembers::where('mid','=',$v->mid)->first();
                if(empty($gr)){
                    echo "delete mid:$v->mid ,id:$v->id \r\n";
                    Cash::destroy($v->id);       
                }
                Redis::set('cashToApplyid',$v);
            }
        }else{
            Redis::set('cashToApplyid',0);
        }
        
    }

  
}