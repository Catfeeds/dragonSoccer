<?php
namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\Area;
use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Groupinvite;
use App\Models\Members;
use App\Models\Gteam;

use Config;
use DB;

class GroupController extends Controller
{
    private $mid = '';
    private $memberArr = '';
    private $statusArr = ['1'=>'报名成功','2'=>'匹配中','3'=>'匹配失败','4'=>'匹配成功'];
    private $statusmsgArr = ['1'=>'已报名,等待队伍分配','2'=>'匹配中，请耐心等待','3'=>'匹配失败','4'=>'进群'];
    private $invitestatusArr = ['1'=>'待接受','2'=>'已同意','3'=>'失效'];  
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
        $this->memberArr = Config::get('custom.member');
	}

    //比赛报名
    public function add(Request $request){
        $gameid = $request->get('gameid','');
        $type = $request->get('type','s');

        $memArr = Members::where(array('id'=>$this->mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>1,'msg'=>'用户不存在'));
            exit();    
        }

        if($memArr->status!='y'){
            return response()->json(array('error'=>1,'msg'=>'请先认证再报名'));
            exit();    
        }

        if(empty($memArr->province) || empty($memArr->city)){
            return response()->json(array('error'=>1,'msg'=>'请填写所在省市'));
            exit();    
        }

        $gArr = Games::with('ages')->where(array('id'=>$gameid))->first();
        if(empty($gArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛不存在'));
            exit();    
        }

        $gaid = '';
        foreach ($gArr->ages as $v) {
            if( strtotime($memArr->birthday)>= $v->starttime && strtotime($memArr->birthday)<= $v->endtime){
                $gaid = $v->id;
            }
        }
        if(empty($gaid)){
            return response()->json(array('error'=>1,'msg'=>'您年龄不适合该比赛'));
            exit();    
        }

        if(time()<=$gArr->applystime || time()>=$gArr->applyetime){
            return response()->json(array('error'=>1,'msg'=>'比赛已经停止报名'));
            exit();    
        }

        $mid = $this->mid;
        /*$groupArr = Group::where(array('gamesagesid'=>$gaid))->whereHas('gmember',function($query) use ($mid){
            $query->select('mid','groupid')->where(array('mid'=>$mid));
        })->first();*/

        $groupArr = Group::where(array('gamesagesid'=>$gaid))->join('group_members', function ($join) use ($mid) {
            $join->on('group.id', '=', 'group_members.groupid')
                 ->where('group_members.mid', '=', $mid);
        })->first();
    

        if(!empty($groupArr)){
            return response()->json(array('error'=>0,'msg'=>'报名成功1','groupid'=>$groupArr->id));
            exit();
        }else{
            $res = false;            
            DB::beginTransaction();
                $p = $memArr->province;
                $c = ($memArr->city=='市辖区'||$memArr->city=='县')?$memArr->country:$memArr->city;
                $r1 = Group::create(['gamesagesid'=>$gaid,'number'=>1,'province'=>$p,'city'=>$c,'type'=>$type ]);
                Groupmembers::create(['groupid'=>$r1->id,'mid'=>$mid,'isleader'=>'y']);
            $res = true;
            DB::commit();
            if($res){
                return response()->json(array('error'=>0,'msg'=>'报名成功','groupid'=>$r1->id));
                exit();
            }
        }
        
        return response()->json(array('error'=>1,'msg'=>'报名失败'));
        exit();
    }

    public function getinfo(Request $request){
        $id = $request->get('groupid',''); 
        if(empty($id)){
            return response()->json(array('error'=>1,'msg'=>'参数不能为空','data'=>[]));
            exit(); 
        }         
        $gArr = Group::with('gamesages','gamesages.games')->find($id);
        $gmArr = Groupmembers::with('members')->where('groupid',$id)->get();

        $dataArr['gname'] = $gArr->gamesages->games->name.'('.$gArr->gamesages->val.')';    
        $dataArr['gedntime'] ='报名结束时间：'. date('Y-m-d',$gArr->gamesages->games->applyetime);    
        $dataArr['gsn'] ='匹配编号：'.$id; 
        $dataArr['groupid'] = $id; 
        $dataArr['status'] = $gArr->status; 
        $dataArr['isleader'] = 'n'; 
        $dataArr['type'] = $gArr->type;  
        $dataArr['msgstatus'] = $this->statusArr[$gArr->status]; 
        if(!empty($gmArr)){
            foreach ($gmArr as $k => $v) {
                $mArr['icon'] = $v->members->icon;
                $mArr['name'] = $v->members->name;
                $mArr['mid'] = $v->mid;
                $mArr['position'] = empty($v->members->position)?'':$this->memberArr['positionArr'][$v->members->position];
                $mArr['isleader'] = $v->isleader;
                if($v->mid == $this->mid && $v->isleader=='y'){
                    $dataArr['isleader'] = 'y';
                }
                $dataArr['members'][] = $mArr;
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }


    public function start(Request $request){
        $id = $request->get('groupid','');  

        if(!Groupmembers::where('groupid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){            
            return response()->json(array('error'=>1,'msg'=>'请联系队长进行操作'));
            exit();  
        }

        if(Group::where('id',$id)->where('status','1')->update(['status'=>'2'])){

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();  
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }


    public function change(Request $request){
        $id = $request->get('groupid','');  
        $leaderid = $request->get('leaderid','');  

        if(!$r = Groupmembers::where('groupid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){            
            return response()->json(array('error'=>1,'msg'=>'请联系队长进行操作'));
            exit();  
        }

        if(Groupmembers::where('groupid',$id)->where('mid',$this->mid)->update(['isleader'=>'n'])){
            if(Groupmembers::where('groupid',$id)->where('mid',$leaderid)->update(['isleader'=>'y'])){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit(); 
            } 
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }


    public function invite(Request $request){
        $id = $request->get('groupid','');  
        $fmid = $request->get('fmid','');
        if(!$r = Groupmembers::where('groupid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){            
            return response()->json(array('error'=>1,'msg'=>'请联系队长进行操作'));
            exit();  
        }

        if($r2 = Groupmembers::where('groupid',$id)->where('mid',$fmid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经处于队伍中'));
            exit();
        }

        $groupArr = Group::where(array('id'=>$id))->whereHas('gmember',function($query) use ($fmid){
            $query->select('mid','groupid')->where(array('mid'=>$fmid));
        })->first();
        if(!empty($groupArr)){
            return response()->json(array('error'=>1,'msg'=>'已经参赛'));
            exit();
        }

        if(Groupinvite::where(['groupid'=>$id,'fmid'=>$fmid,'status'=>'1'])->first()){
            return response()->json(array('error'=>1,'msg'=>'邀请已存在'));
            exit(); 
        }

        if(Groupinvite::create(['groupid'=>$id,'mid'=>$this->mid,'fmid'=>$fmid])){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit(); 
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }


    public function inviteme(Request $request){
        $listArr = Groupinvite::with('members')->where('fmid',$this->mid)->get();
        //var_dump($listArr->toArray());
        $dataArr = [];
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $tmp['icon'] = $v->members->icon;
                $tmp['name'] = $v->members->truename;                
                $tmp['mobile'] = substr($v->members->mobile,0,3).'****'.substr($v->members->mobile,-4);
                $tmp['groupid'] = $v->groupid;
                $tmp['status'] = $v->status;
                $tmp['statusmsg'] = $this->invitestatusArr[$v->status];
                $dataArr[] = $tmp;
            }    
        }
        //var_dump($dataArr);
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }


    public function invitelist(Request $request){
        $id = $request->get('groupid','');
        $listArr = Groupinvite::with('fmembers')->where('groupid',$id)->get();
        $dataArr = [];
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $tmp['icon'] = $v->fmembers->icon;
                $tmp['name'] = $v->fmembers->truename;                
                $tmp['mobile'] = substr($v->fmembers->mobile,0,3).'****'.substr($v->fmembers->mobile,-4);
                $tmp['groupid'] = $v->groupid;
                $tmp['status'] = $v->status;
                $tmp['statusmsg'] = $this->invitestatusArr[$v->status];
                $dataArr[] = $tmp;
            }    
        }
        //var_dump($dataArr);
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    public function inviteyes(Request $request){
        $id = $request->get('groupid','');
        if(Groupinvite::where('groupid',$id)->where('fmid',$this->mid)->where('status','1')->first()){
            $gArr = Group::where('id',$id)->first();
            $res = false;   
            if(!empty($gArr)){
                $gmArr = Groupmembers::where('groupid',$id)->count();
                if($gArr->status=='4' || $gmArr>=12){
                    $res = Groupinvite::where('groupid',$id)->where('fmid',$this->mid)->update(['status'=>'3']);
                }else{
                    DB::beginTransaction();
                        Groupmembers::create(['groupid'=>$id,'mid'=>$this->mid]);
                        Group::where('id','=',$id)->increment('number',1);
                        Groupinvite::where('groupid',$id)->where('fmid',$this->mid)->update(['status'=>'2']);
                    $res = true;
                    DB::commit();
                }
            }

            if($res){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit();
            }
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }

    public function delmember(Request $request){
        $id = $request->get('groupid','');  
        $delmid = $request->get('delmid','');
        if(!$r = Groupmembers::where('groupid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){           
            return response()->json(array('error'=>1,'msg'=>'请联系队长进行操作'));
            exit();  
        }

        if(!$r2 = Groupmembers::where('groupid',$id)->where('mid',$delmid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经删除'));
            exit();
        }

        if(Groupmembers::where(['groupid'=>$id,'mid'=>$delmid])->delete()){
            Group::where('id','=',$id)->decrement('number',1);
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit(); 
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }

    public function exitmember(Request $request){
        $id = $request->get('groupid',''); 
        if(Groupmembers::where('groupid',$id)->count()==1){
            if(Groupmembers::where(['groupid'=>$id,'mid'=>$this->mid])->delete()){
                if(Group::where(['id'=>$id])->delete()){
                    return response()->json(array('error'=>0,'msg'=>'成功'));
                    exit();
                }    
            }
        }else{
            if($r = Groupmembers::where('groupid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){           
                return response()->json(array('error'=>1,'msg'=>'请先移交队长'));
                exit();  
            }

            if(!$r2 = Groupmembers::where('groupid',$id)->where('mid',$this->mid)->first()){            
                return response()->json(array('error'=>1,'msg'=>'已经删除'));
                exit();
            }

            if(Groupmembers::where(['groupid'=>$id,'mid'=>$this->mid])->delete()){
                Group::where('id','=',$id)->decrement('number',1);
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit(); 
            }    
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }


    public function getall(Request $request){
        $listArr = Groupmembers::with('group','group.gamesages.games','group.gamesages.games.school')->where('mid',$this->mid)->get();
        //var_dump($listArr->toArray());
        $dataArr = [];
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $tmp['name'] = $v->group->gamesages->games->name;                
                $tmp['gameid'] = $v->group->gamesages->games->id;                
                $tmp['gametype'] = $v->group->gamesages->games->school->type;                
                $tmp['groupid'] = $v->groupid;
                $tmp['status'] = $v->group->status;
                $tmp['statusmsg'] = $this->statusArr[$v->group->status];

                $tmp['gamesagesid'] = '';
                $tmp['gamesagesidmsg'] = '';
                $tmp['gid'] = '';
                $tmp['gidmsg'] = $this->statusmsgArr[$v->group->status];

                if($v->group->status==4){
                    $tmp['gamesagesid'] = $v->group->gamesagesid;
                    $tmp['gamesagesidmsg'] = '赛程安排';

                    $mid= $this->mid;
                    $gteamArr = Gteam::where('gamesagesid',$v->group->gamesagesid)
                                ->whereHas('teammember',function($query) use ($mid){
                                    $query->select('mid','teamid')->where('mid',$mid);
                                })->first();

                    $tmp['gid'] = empty($gteamArr)?'':$gteamArr->gid;
                    $tmp['gidmsg'] = empty($gteamArr)?'':'进群';
                }
                $dataArr[] = $tmp;
            }    
        }
        //var_dump($dataArr);
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }
}