<?php
namespace App\Http\Controllers\Shell;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;
use App\Models\Matchinfo;
use App\Models\Members;
use Illuminate\Support\Facades\Redis;
use App\Models\Games;
use App\Models\Gamecontent;

use App\Models\Apply;
use App\Models\Group;
use App\Models\Groupmembers;

use Config;
use DB;
use Hash;
class GroupController extends Controller{
    //数据同步
    public function makeGroupData(){
        //['1'=>'报名成功','2'=>'已结束','3'=>'失败','4'=>'解散','5'=>'完成组队','6'=>'匹配中','7'=>'匹配中','8'=>'匹配成功']
        //['1'=>'报名成功','2'=>'匹配中','3'=>'匹配失败','4'=>'匹配成功'];
        $id = Redis::get('makeGroupDataId');
        var_dump($id);
        $applyArr = Apply::whereIn('status',['1','5','6'])->where('id','>',empty($id)?0:$id)->where('friend_mid','=','0')->limit(100)->get();
        //var_dump($applyArr->toArray());
        if(!empty($applyArr)){
            foreach($applyArr as $k => $v){
                if($v->matchid=='10'){ //青龙
                    $gaid = 1;
                }
                if($v->matchid=='9'){ //银龙
                    $gaid = 2;
                }
                if($v->matchid=='8'){ //金龙
                    $gaid = 3;
                }

                if($v->status=='1' || $v->status=='5'){ //金龙
                    $status = 1;
                }
                if($v->status=='6'){ //金龙
                    $status = 2;
                }
                if(!Groupmembers::where(['mid'=>$v->mid])->first()){
                    DB::beginTransaction();
                        $gr = Group::create(['gamesagesid'=>$gaid,'number'=>1,'province'=>$v->province,'city'=>$v->city,'type'=>'s','status'=>$status ]);                        
                        Groupmembers::create(['groupid'=>$gr->id,'mid'=>$v->mid,'isleader'=>'y']);
                        if($fArr = Apply::whereIn('status',['1','5','6'])->where('friend_mid','=',$v->mid)->first()){
                            Groupmembers::create(['groupid'=>$gr->id,'mid'=>$fArr->mid,'isleader'=>'n']);
                            echo "id: $v->id,mid:$v->mid,fmid:$fArr->mid--create--".$res."<br>\r\n"; 
                        }
                        $res = true;
                    DB::commit();
                    echo "id: $v->id,mid:$v->mid--create--".$res."<br>\r\n";    
                }
                
                Redis::set('makeGroupDataId',$v->id);    
            }
        }
    }
}
