<?php
namespace App\Http\Controllers\Shell;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;
use App\Helpers\EasemobHelper;

use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Gteam;
use App\Models\Gteammembers;

use Hash;
use DB;
use Config;
class GteamController extends Controller
{
    private $mid = '';
    private $memberArr = '';
    private $systemArr = '';
    private $teamArr = '';
    public function __construct(Request $request){
        $this->mid = $request->get('mid','');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
        $this->teamArr = Config::get('custom.team');
    }
     
    public function maketeamfor7(){
        $id = Redis::get('gteamMakeTeamfor7id');
        $gamesagesid = Gamesages::whereHas('games',function($querry){ $querry->whereHas('school',function($querry){ $querry->where('type','l');}); })->pluck('id')->toArray();
        //var_dump($gamesagesid);
        if(!empty($gamesagesid)){
            try { 
                $gArr = Group::where('id','>',empty($id)?0:$id )->where('status','=','2')->whereIn('gamesagesid',$gamesagesid)->where('number','>=','7')->first(); 
                //var_dump($gArr);
                if(!empty($gArr)){
                    $easemobGids = array(); //环信用户id
                    $owerid = ''; //环信用户id

                    $res = false;            
                    DB::beginTransaction();
                        $insert['type'] = 'm';        
                        $insert['gamesagesid'] = $gArr->gamesagesid;
                        $insert['province'] = $gArr->province;
                        $insert['city'] = $gArr->city; 
                        $insert['name'] = $gArr->city.rand(1000,9999); 
                        $r1 = Gteam::create($insert);
                        $g_teamid = $r1->id;
                        $g_teamname = $r1->name;
                        $gmArr =Groupmembers::where(['groupid'=>$gArr->id])->get();
                        foreach ($gmArr as $kk=> $vv) {
                            Gteammembers::create(['teamid'=>$r1->id,'mid'=>$vv->mid,'isleader'=>$kk==0?'y':'n' ]); 
                            $kk==0?$owerid = $this->memberArr['easemobArr']['member'].$vv->mid:'';
                            $easemobGids[] = $this->memberArr['easemobArr']['member'].$vv->mid; //环信用户id  
                        }
                        Group::where('id','=',$gArr->id)->update(['status'=>4]);
                        
                    $res = true;
                    DB::commit();  
                    if($res){
                        echo "id={$gArr->id},组队成功,success\r\n";
                        Redis::set('gteamMakeTeamfor7id',$gArr->id); 

                        $gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$g_teamid,$g_teamname,$owerid,$easemobGids);//环信
                        //var_dump($gid);
                        if(!empty($gid)){
                            if(Gteam::where(array('id'=>$g_teamid))->update(array('gid'=>$gid))){
                                //系统消息
                                EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
                                EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信

                                $r = EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],array($gid),'匹配成功',$this->systemArr['easemobtypeArr']['group']); //环信

                                echo "id={$g_teamid} ,gid={$gid},环信,success\r\n";  
                            }   
                        }


                    }else{
                        echo "id={$gArr->id},组队失败，failed \r\n";
                    }     
                }
                Redis::set('gteamMakeTeamfor7id','0');
                echo "组队条件不符合，none----\r\n";
            } catch (Exception $e) {   
                echo "组队报错----，error\r\n";
            }         
        }

        echo "---maketeamfor7-----end\r\n";
    }

    public function maketeam(){
        //Redis::set('gteamMakeTeamid','0');
        $id = Redis::get('gteamMakeTeamid');
        var_dump($id);
        $gamesagesid = Gamesages::whereHas('games',function($querry){ $querry->whereHas('school',function($querry){ $querry->where('type','l');}); })->pluck('id')->toArray();
        //var_dump($gamesagesid);
        if(!empty($gamesagesid)){
            try { 
                $gArr = Group::where('id','>',empty($id)?0:$id )->where('status','=','2')->whereIn('gamesagesid',$gamesagesid)->where('number','<','7')->first();
                //var_dump($gArr->toArray()); 
                if(!empty($gArr)){
                    $easemobGids = array(); //环信用户id
                    $owerid = ''; //环信用户id

                    $number = 7-$gArr->number;
                    $groupids = [$gArr->id];
                    $tmpid = $gArr->id;
                    for ($i=0; $i<=6; $i++) { 
                        $g2Arr = Group::where('status','=','2')->whereNotIn('id',$groupids)->where('id','>',$tmpid)->where('gamesagesid',$gArr->gamesagesid)->where('number','<=',$number)->where('province',$gArr->province)->where('city',$gArr->city)->first();
                        if(!empty($g2Arr)){
                            echo "$i---$g2Arr->id---$number\r\n";
                            $number -= $g2Arr->number;
                            if($number >= 0){
                                $groupids[] = $g2Arr->id;
                            }else{
                                break;  
                            }
                            $tmpid = $g2Arr->id;
                            echo "$i---===$g2Arr->id===---$number\r\n";
                        }                        
                    }
                    if($number!=0){
                        $groupids = [];
                    }
                    var_dump($groupids);
                    if(!empty($groupids)){
                        $res = false;            
                        DB::beginTransaction();
                            $insert['type'] = 'm';        
                            $insert['gamesagesid'] = $gArr->gamesagesid;
                            $insert['province'] = $gArr->province;
                            $insert['city'] = $gArr->city; 
                            $insert['name'] = $gArr->city.rand(1000,9999); 
                            $r1 = Gteam::create($insert);
                            $g_teamid = $r1->id;
                            $g_teamname = $r1->name;

                            foreach ($groupids as $k => $v) {
                                $gmArr = Groupmembers::where(['groupid'=>$v])->get();
                                foreach ($gmArr as $kk=> $vv) {
                                    Gteammembers::create(['teamid'=>$r1->id,'mid'=>$vv->mid,'isleader'=> ($k==0&&$kk==0)?'y':'n' ]); 
                                    ($k==0&&$kk==0)?$owerid = $this->memberArr['easemobArr']['member'].$vv->mid:'';
                                    $easemobGids[] = $this->memberArr['easemobArr']['member'].$vv->mid; //环信用户id  
                                }
                                Group::where('id','=',$v)->update(['status'=>4]);
                            }
                        $res = true;
                        DB::commit();  
                        if($res){
                            echo "id=".json_encode($groupids).",组队成功,success\r\n";

                            $gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$g_teamid,$g_teamname,$owerid,$easemobGids);//环信
                            //var_dump($gid);
                            if(!empty($gid)){
                                if(Gteam::where(array('id'=>$g_teamid))->update(array('gid'=>$gid))){
                                    //系统消息
                                    EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
                                    EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信
                                    $r = EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],array($gid),'匹配成功',$this->systemArr['easemobtypeArr']['group']); //环信
                                    echo "id={$g_teamid} ,gid={$gid},环信,success\r\n";  
                                }   
                            }                          
                        }else{
                            echo "id={$groupids},组队失败，failed \r\n";
                        }
                    }

                    Redis::set('gteamMakeTeamid',$gArr->id);                      
                }else{
                    Redis::set('gteamMakeTeamid','0');
                    echo "组队条件不符合，none----\r\n";  
                }                
            } catch (Exception $e) {   
                echo "组队报错----，error\r\n";
            }
        }
        echo "---maketeam-----end\r\n";           
    }
}