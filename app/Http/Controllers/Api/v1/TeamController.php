<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use App\Models\Match;
use App\Models\Relation;
use App\Models\Comment;
use App\Models\Apply;
use App\Models\Applyinvite;
use App\Helpers\EasemobHelper;
use App\Models\Team;
use App\Models\Teammember;

use Hash;
use DB;
use Config;
class TeamController extends Controller
{
    private $mid = '';
    private $teamArr = '';
    private $memberArr = '';
    private $systemArr = '';
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
        $this->teamArr = Config::get('custom.team');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
	}

    public function listteam(Request $request){
        $teamid = $request->get('teamid','');
        $listArr = Team::where('id','=',$teamid)->first(); 
        $listmemberYArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->limit(2)->get(); 
        //var_dump($listArr->teammember->toArray());
        $dataArr  = array();
        if(!empty($listArr)){
            $dataArr['teamid'] = $listArr->id;
            $dataArr['icon'] = $listArr->icon;
            $dataArr['name'] = $listArr->name;
            $dataArr['type'] = $listArr->type;
            $dataArr['gid'] = $listArr->gid;
            $dataArr['managemid'] = $listmemberYArr->mid;
            $dataArr['membernumber'] = (empty($listmemberArr) && empty($listmemberYArr))?1:count($listmemberArr)+1;
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr['member'][$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr['member'][$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr['member'][$i]['name'] = $listmemberYArr->name;
                    $dataArr['member'][$i]['isleader'] = 'y';   
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr['member'][$i]['mid'] = $v->member->id;
                    $dataArr['member'][$i]['icon'] = $v->member->icon;
                    $dataArr['member'][$i]['name'] = $v->name;
                    $dataArr['member'][$i]['isleader'] = 'n';  
                    $i++;
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    //所有成员
    public function listteammember(Request $request){
        $teamid = $request->get('teamid','');
        $listArr = Team::where('id','=',$teamid)->first(); 
        $listmemberYArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->get(); 
        $dataArr  = array();
        if(!empty($listArr)){
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr[$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr[$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr[$i]['name'] = $listmemberYArr->name;
                    $dataArr[$i]['isleader'] = $listmemberYArr->isleader;  
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr[$i]['mid'] = $v->member->id;
                    $dataArr[$i]['icon'] = $v->member->icon;
                    $dataArr[$i]['name'] = $v->name;
                    $dataArr[$i]['isleader'] = $v->isleader;  
                    $i++;
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    public function listteaminfo(Request $request){ //??????
        $teamid = $request->get('viewid','');
        $listArr = Team::where('gid','=',$teamid)->first(); 
        $dataArr  = array();
        if(!empty($listArr)){
            $dataArr['mid'] = $listArr->id; //ios强制修改key
            $dataArr['icon'] = $listArr->icon;
            $dataArr['name'] = $listArr->name;            
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }
    
    //自由组队
    public function creatteam(Request $request){
        $mids = $request->get('mids','');
        if(gettype($mids)!='array'){
            return response()->json(array('error'=>1,'msg'=>'参数错误'));
            exit();
        }

        if(count($mids)<2 ){
            return response()->json(array('error'=>1,'msg'=>'人数太少'));
            exit();
        }

        $insertMemArr = array();
        $easemobGids = array();
        if(!empty($mids)){
            foreach ($mids as $k => $v) {
                $memArr = '';
                $memArr = Members::where(array('id'=>$v))->first();
                if(empty($memArr)){
                    return response()->json(array('error'=>2,'msg'=>'用户参数有误'));
                    exit();    
                }else{
                    $insertMemArr[$k]['mid'] = $v;   
                    $insertMemArr[$k]['name'] = $memArr->name;
                    $easemobGids[] = $this->memberArr['easemobArr']['member'].$v;    
                }    
            }
        }

        $curmemArr = Members::where(array('id'=>$this->mid))->first();
        
        $res = false;
        $teamid = '';
        $teamname = '';
        $teamMemberName = '';
        DB::beginTransaction();
            //修改当前状态
            $teamname = $this->teamArr['teamnameArr']['f'].$curmemArr->name.date('dHi');
            $r = Team::create(array('name'=>$teamname));
            $teamid = $r->id;  

            //添加
            foreach ($insertMemArr as $k => $v) {
                $v['teamid'] = $r->id;
                Teammember::create($v);
                $teamMemberName .= $v['name']."|" ;
            }

            $curMemArr['mid'] = $this->mid;   
            $curMemArr['name'] = $curmemArr->name;
            $curMemArr['teamid'] = $r->id; 
            $curMemArr['isleader'] = 'y';
            Teammember::create($curMemArr);

            $res = true;  
        DB::commit();
        if($res){
            $gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$teamid,$teamname,$this->memberArr['easemobArr']['member'].$this->mid,$easemobGids);//环信
            //var_dump($gid);
            if(!empty($gid)){
                if(Team::where(array('id'=>$teamid))->update(array('gid'=>$gid))){
                    //系统消息
                    EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
                    EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信

                    $r = EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],array($gid),$teamMemberName,$this->systemArr['easemobtypeArr']['group']); //环信
                    //var_dump($r);    
                }   
            }

            return response()->json(array('error'=>0,'msg'=>'成功'));     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }

    //组队好友列表
    public function teamlistrelation(Request $request){
        $teamid = $request->get('teamid','');

        $listmidArr = Relation::where('mid','=',$this->mid)->where('status','=','4')->pluck('friend_mid')->toArray();
        $listfmidArr = Relation::where('friend_mid','=',$this->mid)->where('status','=','4')->pluck('mid')->toArray();

        $idsArr  = array();
        if(!empty($listmidArr)){
            $idsArr  = $listmidArr;
        }

        if(!empty($idsArr)){
            if(!empty($listfmidArr)){
                $idsArr  = array_unique( array_merge($idsArr,$listfmidArr) );
            }
        }else{
            if(!empty($listfmidArr)){
                $idsArr  = $listfmidArr;
            }
        }

        $teammidArr = Teammember::where('teamid','=',$teamid)->pluck('mid')->toArray();
        $idsArr = array_diff($idsArr,$teammidArr);

        $dataArr  = array();
        if(!empty($idsArr)){
            $listArr = Members::whereIn('id',$idsArr)->get();
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['mid'] = $v->id;
                $dataArr[$k]['icon'] = $v->icon;
                $dataArr[$k]['name'] = $v->name;
                $dataArr[$k]['mobile'] = FunctionHelper::makemobilestar($v->mobile);
                //$dataArr[$k]['easemobtype'] = $this->memberArr['easemobtypeArr']['member'];
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //添加队员
    public function addteammember(Request $request){
        $mids = $request->get('mids','');
        $teamid = $request->get('teamid','');

        if(!$teamArr = Team::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }
        if(!empty($teamArr) && $teamArr->type=='m'){
            return response()->json(array('error'=>1,'msg'=>'比赛群，无权添加成员'));
            exit(); 
        }

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'不是该群成员'));
            exit(); 
        }

        $insertMemArr = array();
        $easemobGids = array();
        if(!empty($mids)){
            foreach ($mids as $k => $v) {
                $memArr = '';
                $memArr = Members::where(array('id'=>$v))->first();
                if(empty($memArr)){
                    return response()->json(array('error'=>2,'msg'=>'用户参数有误'));
                    exit();    
                }else{
                    if(Teammember::where('teamid','=',$teamid)->where('mid','=',$v)->first()){
                        return response()->json(array('error'=>1,'msg'=>'已经是该群成员'));
                        exit(); 
                    }
                    $insertMemArr[$k]['mid'] = $v;   
                    $insertMemArr[$k]['name'] = $memArr->name;
                    $insertMemArr[$k]['teamid'] = $teamid;

                    $easemobGids[] = $this->memberArr['easemobArr']['member'].$v;    
                }    
            }
        }
       
        $res = false;
        DB::beginTransaction();
            foreach ($insertMemArr as $k => $v) {
                Teammember::create($v);
            }
            $res = true;  
        DB::commit();
        if($res){
            EasemobHelper::addGroupsUser($teamArr->gid,$easemobGids);//环信

            //系统消息
            EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
            EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }


    //删除队员
    public function delteammember(Request $request){
        $mid = $request->get('delmid','');
        $teamid = $request->get('teamid','');

        if(!$teamArr = Team::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }
        if(!empty($teamArr) && $teamArr->type=='m'){
            return response()->json(array('error'=>1,'msg'=>'比赛群，无权删除队员'));
            exit(); 
        }

        if($mid==$this->mid){
            return response()->json(array('error'=>1,'msg'=>'无权删除队员'));
            exit();     
        }

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'无权删除队员'));
            exit(); 
        }

        

        $memArr = Members::where(array('id'=>$mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>2,'msg'=>'用户参数有误'));
            exit();    
        }

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经不是该群成员'));
            exit(); 
        }
      
        if( Teammember::where(array('teamid'=>$teamid,'mid'=>$mid))->delete() ){
            EasemobHelper::deleteFriend($teamArr->gid,$this->memberArr['easemobArr']['member'].$mid);//环信

            //系统消息
            EasemobHelper::addUser($this->systemArr['easemobArr']['delgroup'],md5($this->systemArr['easemobArr']['delgroup']),$this->systemArr['easemobArr']['delgroup']); //环信
            EasemobHelper::sendMsg($this->systemArr['easemobArr']['delgroup'],array($this->memberArr['easemobArr']['member'].$mid),$this->systemArr['easemobmsgArr']['delgroup'].$teamArr->name); //环信

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }


    //退出群组
    public function quitteammember(Request $request){
        $teamid = $request->get('teamid','');
        if(!$teamArr = Team::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }
        $rltArr = array('error'=>1,'msg'=>'群组不存在');
        if($teamArr->type=='f'){
            $rltArr = $this->quitteam_f($teamid);
        }

        if($teamArr->type=='m'){
            $rltArr = $this->quitteam_m($teamid);
        }
        
        return response()->json($rltArr);
        exit(); 

    }

    //退出自由群组
    private function quitteam_f($teamid){
        $teamArr = Team::where('id','=',$teamid)->first();

        $tmnum = Teammember::where('teamid','=',$teamid)->count();
        $tmArr = Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first();//->where('isleader','=','y')

        if(empty($tmArr)){
            return array('error'=>1,'msg'=>'已经不是该群成员');
            exit(); 
        }

        if($tmArr->isleader=='y'){
            if($tmnum==1){
                $res = false;
                DB::beginTransaction();
                    Teammember::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete();
                    Team::destroy((int)$teamid);
                    $res = true;  
                DB::commit();
                if($res){
                    //删除群组
                    EasemobHelper::delGroup($teamArr->gid);//环信
                    return array('error'=>0,'msg'=>'成功');    
                }
                return array('error'=>1,'msg'=>'失败');
                exit();
            }
            return array('error'=>1,'msg'=>'请先转交群组管理权限');
            exit(); 
        }
      
        if( Teammember::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete() ){
            EasemobHelper::deleteFriend($teamArr->gid,$this->memberArr['easemobArr']['member'].$this->mid);//环信

            return array('error'=>0,'msg'=>'成功'); 
            exit();    
        }
        return array('error'=>1,'msg'=>'失败');
        exit(); 

    }

    //退出比赛群
    private function quitteam_m($teamid){
        $teamArr = Team::where('id','=',$teamid)->first();
        
        $tmArr = Teammember::where('teamid','=',$teamid)->pluck('mid')->toArray();
        if(empty($tmArr) || !in_array($this->mid,$tmArr)){
            return array('error'=>1,'msg'=>'已经不是该群成员');
            exit(); 
        }

        $tnum = Team::where('deletemid','=',$this->mid)->where('matchid','=',$teamArr->matchid)->withTrashed()->count();
        if($tnum>1){ //????????????
            return array('error'=>1,'msg'=>'已经退出多次，系统不允许退出');
            exit(); 
        }
        
        $res = false;
        DB::beginTransaction();
            //删除apply
            Apply::where(array('mid'=>$this->mid,'matchid'=>$teamArr->matchid))->delete();
            foreach ($tmArr as $v) {
                Apply::where(array('mid'=>$v,'matchid'=>$teamArr->matchid))->update(array('status'=>'7'));    
            }

            //删除队伍
            Teammember::where(array('teamid'=>$teamid))->delete();
            Team::where('id','=',$teamid)->update(array('deletemid'=>$this->mid));
            Team::destroy((int)$teamid);
            $res = true;  
        DB::commit();
        if($res){
            //删除群组
            EasemobHelper::delGroup($teamArr->gid);//环信
            return array('error'=>0,'msg'=>'成功');    
        }
        return array('error'=>1,'msg'=>'失败');
        exit();
    }

    //修改群主
    public function changeteamleader(Request $request){
        $teamid = $request->get('teamid','');
        $mid = $request->get('changemid','');

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'无权操作'));
            exit(); 
        }

        if(!$teamArr = Team::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经不是该群成员'));
            exit(); 
        }

        if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'推荐人已经不是该群成员'));
            exit(); 
        }
      
        $res = false;
        DB::beginTransaction();
            Teammember::where(array('teamid'=>$teamid,'mid'=>$this->mid,'isleader'=>'y'))->update(array('isleader'=>'n'));
            Teammember::where(array('teamid'=>$teamid,'mid'=>$mid,'isleader'=>'n'))->update(array('isleader'=>'y'));
            $res = true;  
        DB::commit();
        if($res){
            //系统消息
            EasemobHelper::addUser($this->systemArr['easemobArr']['changegroupleader'],md5($this->systemArr['easemobArr']['changegroupleader']),$this->systemArr['easemobArr']['changegroupleader']); //环信
            EasemobHelper::sendMsg($this->systemArr['easemobArr']['changegroupleader'],array($this->memberArr['easemobArr']['member'].$mid),$this->systemArr['easemobmsgArr']['changegroupleader'].$teamArr->name); //环信

            return response()->json(array('error'=>0,'msg'=>'成功')); 
            exit();    
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();
    }

    //修改群头像
    public function updateteamicon(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','');

        return response()->json($this->changeteaminfo($teamid,array('icon'=>$val)));
        exit();
    }

    //修改名称
    public function updateteamname(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','');
        if(empty($val)){
            if(mb_strlen($name,'utf8')>6){
                return response()->json(array('error'=>1,'msg'=>'名字不能超过8个字'));
                exit();
            }

            if(Team::where('name','=',$name)->where('id','!=',$teamid)->first()){
                return response()->json(array('error'=>1,'msg'=>'名字已被占用'));
                exit();
            }  

            return response()->json(array('error'=>1,'msg'=>'参数不能为空'));
            exit(); 
        }

        return response()->json($this->changeteaminfo($teamid,array('name'=>$val)));
        exit();
    }

    //修改群内名称
    public function updatemembername(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','');
        if(empty($val)){
            return response()->json(array('error'=>1,'msg'=>'参数不能为空'));            
            exit(); 
        }

        return response()->json($this->changeteaminfo($teamid,array('name'=>$val),'m'));
        exit();
    }

    //修改消息面打扰
    public function updateisshowmsg(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','n');

        return response()->json($this->changeteaminfo($teamid,array('isshowmsg'=> ($val=='y'?'y':'n')),'m') );
        exit();
    }

    //修改显示成员名称
    public function updateisshowname(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','n');

        return response()->json($this->changeteaminfo($teamid,array('isshowname'=> ($val=='y'?'y':'n')),'m') );
        exit();
    }

    //修改球衣编号 10-27
    public function updatenumber(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val');
        if(empty($val)){
            return response()->json(array('error'=>1,'msg'=>'球衣编号不能为空')); 
            exit(); 
        }
        if(Teammember::where('teamid','=',$teamid)->where('mid','!=',$this->mid)->where('number','=',$val)->first() ){
            return response()->json(array('error'=>1,'msg'=>'球衣编号已经有小伙伴使用了')); 
            exit();     
        }  
        return response()->json($this->changeteaminfo($teamid,array('number'=> $val),'m') );
        exit();
    }


    //修改信息
    private function changeteaminfo($teamid,$val,$type='t'){
        if(!$teamArr = Team::where('id','=',$teamid)->first()){
            return array('error'=>1,'msg'=>'群组不存在');
            exit(); 
        }

        if($type=='t'){
            if(!Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
                return array('error'=>1,'msg'=>'无权操作');
                exit(); 
            }

            if(Team::where('id','=',$teamid)->update($val)){
                return array('error'=>0,'msg'=>'成功');
                exit();     
            }
        }

        if($type=='m'){
            if(Teammember::where('teamid','=',$teamid)->where('mid','=',$this->mid)->update($val) ){
                return array('error'=>0,'msg'=>'成功');
                exit();     
            }   
        }

        return array('error'=>1,'msg'=>'失败');
        exit();
    }

    
}