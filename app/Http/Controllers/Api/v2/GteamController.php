<?php
namespace App\Http\Controllers\Api\v2;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Helpers\EasemobHelper;

use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Gamecontent;
use App\Models\School;
use App\Models\Gteam;
use App\Models\Gteammembers;
use App\Models\Members;
use App\Models\Relation;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Gteaminvite;
use Config;
use DB;
class GteamController extends Controller{	    
    private $mid = '';
    private $memberArr = '';
    private $systemArr = '';
    private $teamArr = '';

    private $statusArr = ['w'=>'匹配成功','ww'=>'待定','s'=>'晋级','f'=>'淘汰'];
    private $invitestatusArr = ['1'=>'待接受','2'=>'已同意','3'=>'失效']; 
    public function __construct(Request $request){
        $this->mid = $request->get('mid','');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
        $this->teamArr = Config::get('custom.team');
    }

    //所有队伍
    public function getall(Request $request){
        $maxid = Gteam::where('type','m')->max('id');
        $number = $request->get('number','');
        $lastid = $request->get('lastid',$maxid+1);
        empty($lastid)?$lastid=$maxid+1:'';
        if(!empty($number)){
            $gtArr = Gteam::with('gamesages')->where('id','<',$lastid)->where('type','m')->orderBy('id', 'desc')->limit($number)->get();
        }else{
            $gtArr = Gteam::with('gamesages')->where('id','<',$lastid)->where('type','m')->orderBy('id', 'desc')->get();
        }
        //var_dump($number);
        $dataArr = ['teams'=>[]];
        if($gtArr){
            foreach ($gtArr as $k => $v) {
                $tmp['teamid'] = $v->id;
                $tmp['icon'] = empty($v->icon)?'':$v->icon;
                $tmp['name'] = empty($v->name)?'':$v->name;
                $tmp['membernumber'] = Gteammembers::where('teamid',$v->id)->count();
                $tmp['gamename'] = empty($v->gamesages)?'':$v->gamesages->val;


                $dataArr['teams'][] = $tmp; 
            }
            $dataArr['number'] = Gteam::where('type','m')->count();           
        }       

        return response()->json(array('error'=>0,'msg'=>'成功','data'=> $dataArr));
        exit();
    }


    //我的所有队伍
    public function getallme(){
        $mid = $this->mid;
        $gtArr = Gteam::where('type','m')->with('gamesages','gamesages.games')->whereHas('teammember',function($query) use ($mid){
                    $query->select('mid','teamid')->where('mid',$mid);
                })->get();

        $dataArr = [];
        if($gtArr){
            foreach ($gtArr as $k => $v) {
                $tmp['teamid'] = $v->id;
                $tmp['gamename'] = empty($v->gamesages->games->name)?'':$v->gamesages->games->name;
                $tmp['name'] = empty($v->name)?'':$v->name;
                $tmp['status'] = $this->statusArr[$v->status];
                $dataArr[] = $tmp; 
            }         
        }       

        return response()->json(array('error'=>0,'msg'=>'成功','data'=> $dataArr));
        exit();
    }

    //添加收藏
    public function school(Request $request){
        $id = $request->get('id','');
        $dataArr = [];
        if($gaArr = Gamesages::where('gamesid','=',$id)->get()){
    		foreach ($gaArr as $k => $v) {
    			$data = $this->makeLogData($v->id);
    			$dataArr = array_merge($dataArr,$data);
    		}
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
    }


    private function makeLogData($id){
    	$gtArr = Gteam::where('gamesagesid','=',$id)->get();
        $dataArr = [];
		if($gtArr){
			foreach ($gtArr as $k => $v) {
				$tmp['teamid'] = $v->id;
                $tmp['icon'] = empty($v->icon)?'':$v->icon;
                $tmp['name'] = empty($v->name)?'':$v->name;
	        	$tmp['number'] = Gteammembers::where('teamid',$v->id)->count();

                $dataArr[] = $tmp; 
			}			
        }       

    	return array_values($dataArr);    
    }

    public function viewinfo(Request $request){
        $viewid = $request->get('viewid','');
        $listArr = Gteam::where('gid','=',$viewid)->first(); 
        $dataArr  = array();
        if(!empty($listArr)){
            $dataArr['mid'] = $listArr->id;
            $dataArr['icon'] = $listArr->icon;
            $dataArr['name'] = $listArr->name;            
        }
        return response()->json(array('error'=>empty($dataArr)?1:0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    public function detail(Request $request){
        $teamid = $request->get('teamid','');
        $listArr = Gteam::with('gamesages','gamesages.games')->where('id','=',$teamid)->first(); 
        $listmemberYArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->get(); 
        //var_dump($listArr->teammember->toArray());
        $dataArr  = array();
        if(!empty($listArr)){
            $dataArr['teamid'] = $listArr->id;
            $dataArr['gamename'] = $listArr->gamesages->games->name.$listArr->gamesages->val;
            $dataArr['gametime'] = empty($listArr->gamesages->games->starttime)?'':date('Y年m月d日',$listArr->gamesages->games->starttime);
            $dataArr['gamesn'] = date('YmdHi',strtotime($listArr->created_at)).'#'.$listArr->id;
            $dataArr['type'] = $listArr->type;
            $dataArr['gid'] = $listArr->gid;
            $dataArr['managemid'] = $listmemberYArr->mid;
            $dataArr['membernumber'] = (empty($listmemberArr) && empty($listmemberYArr))?1:count($listmemberArr)+1;
            $dataArr['status'] = $listArr->status;
            $dataArr['statusmsg'] = $this->statusArr[$listArr->status];
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr['member'][$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr['member'][$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr['member'][$i]['name'] = empty($listmemberYArr->member->truename)?FunctionHelper::makemobilestar($listmemberYArr->member->mobile):$listmemberYArr->member->truename;
                    $dataArr['member'][$i]['isleader'] = 'y';   
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr['member'][$i]['mid'] = $v->member->id;
                    $dataArr['member'][$i]['icon'] = $v->member->icon;
                    $dataArr['member'][$i]['name'] = empty($v->member->truename)?FunctionHelper::makemobilestar($v->member->mobile):$v->member->truename;
                    $dataArr['member'][$i]['isleader'] = 'n';  
                    $i++;
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    public function info(Request $request){
        $teamid = $request->get('teamid','');
        $listArr = Gteam::where('id','=',$teamid)->first(); 
        $listmemberYArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->limit(2)->get(); 
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
                    $dataArr['member'][$i]['name'] = empty($listmemberYArr->member->truename)?FunctionHelper::makemobilestar($listmemberYArr->member->mobile):$listmemberYArr->member->truename;
                    $dataArr['member'][$i]['isleader'] = 'y';   
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr['member'][$i]['mid'] = $v->member->id;
                    $dataArr['member'][$i]['icon'] = $v->member->icon;
                    $dataArr['member'][$i]['name'] = empty($v->member->truename)?FunctionHelper::makemobilestar($v->member->mobile):$v->member->truename;
                    $dataArr['member'][$i]['isleader'] = 'n';  
                    $i++;
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    //所有成员
    public function allmembers(Request $request){
        $teamid = $request->get('teamid','');
        $listArr = Gteam::where('id','=',$teamid)->first(); 
        $listmemberYArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Gteammembers::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->get(); 
        $dataArr  = array();
        if(!empty($listArr)){
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr[$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr[$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr[$i]['name'] = empty($listmemberYArr->member->truename)?FunctionHelper::makemobilestar($listmemberYArr->member->mobile):$listmemberYArr->member->truename;
                    $dataArr[$i]['isleader'] = $listmemberYArr->isleader;  
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr[$i]['mid'] = $v->member->id;
                    $dataArr[$i]['icon'] = $v->member->icon;
                    $dataArr[$i]['name'] =  empty($v->member->truename)?FunctionHelper::makemobilestar($v->member->mobile):$v->member->truename;
                    $dataArr[$i]['isleader'] = $v->isleader;  
                    $i++;
                }
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();   
    }

    //自由组队
    public function creat(Request $request){
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
            $r = Gteam::create(array('name'=>$teamname));
            $teamid = $r->id;  

            //添加
            foreach ($insertMemArr as $k => $v) {
                $v['teamid'] = $r->id;
                Gteammembers::create($v);
                $teamMemberName .= $v['name']."|" ;
            }

            $curMemArr['mid'] = $this->mid;   
            $curMemArr['name'] = $curmemArr->name;
            $curMemArr['teamid'] = $r->id; 
            $curMemArr['isleader'] = 'y';
            Gteammembers::create($curMemArr);

            $res = true;  
        DB::commit();
        if($res){
            $gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$teamid,$teamname,$this->memberArr['easemobArr']['member'].$this->mid,$easemobGids);//环信
            //var_dump($gid);
            if(!empty($gid)){
                if(Gteam::where(array('id'=>$teamid))->update(array('gid'=>$gid))){
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
    public function listrelation(Request $request){
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

        $teammidArr = Gteammembers::where('teamid','=',$teamid)->pluck('mid')->toArray();
        $idsArr = array_diff($idsArr,$teammidArr);

        $dataArr  = array();
        if(!empty($idsArr)){
            $listArr = Members::whereIn('id',$idsArr)->get();
            foreach ($listArr as $k => $v) {
                $dataArr[$k]['mid'] = $v->id;
                $dataArr[$k]['icon'] = $v->icon;
                $dataArr[$k]['name'] = empty($v->truename)?FunctionHelper::makemobilestar($v->mobile):$v->truename;
                $dataArr[$k]['mobile'] = FunctionHelper::makemobilestar($v->mobile);
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

    //添加队员
    public function addmember(Request $request){
        $mids = $request->get('mids','');
        $teamid = $request->get('teamid','');

        if(!$teamArr = Gteam::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }
        if(!empty($teamArr) && $teamArr->type=='m'){
            return response()->json(array('error'=>1,'msg'=>'比赛群，无权添加成员'));
            exit(); 
        }

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first()){
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
                    if(Gteammembers::where('teamid','=',$teamid)->where('mid','=',$v)->first()){
                        return response()->json(array('error'=>1,'msg'=>'已经是该群成员'));
                        exit(); 
                    }
                    $insertMemArr[$k]['mid'] = $v;   
                    $insertMemArr[$k]['name'] = $memArr->truename;
                    $insertMemArr[$k]['teamid'] = $teamid;

                    $easemobGids[] = $this->memberArr['easemobArr']['member'].$v;    
                }    
            }
        }
       
        $res = false;
        DB::beginTransaction();
            foreach ($insertMemArr as $k => $v) {
                Gteammembers::create($v);
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
    public function delmember(Request $request){
        $mid = $request->get('delmid','');
        $teamid = $request->get('teamid','');

        if(!$teamArr = Gteam::where('id','=',$teamid)->first()){
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

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'无权删除队员'));
            exit(); 
        }

        

        $memArr = Members::where(array('id'=>$mid))->first();
        if(empty($memArr)){
            return response()->json(array('error'=>2,'msg'=>'用户参数有误'));
            exit();    
        }

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经不是该群成员'));
            exit(); 
        }
      
        if( Gteammembers::where(array('teamid'=>$teamid,'mid'=>$mid))->delete() ){
            EasemobHelper::delGroupsUser($teamArr->gid,$this->memberArr['easemobArr']['member'].$mid);//环信

            //系统消息
            EasemobHelper::addUser($this->systemArr['easemobArr']['delgroup'],md5($this->systemArr['easemobArr']['delgroup']),$this->systemArr['easemobArr']['delgroup']); //环信
            EasemobHelper::sendMsg($this->systemArr['easemobArr']['delgroup'],array($this->memberArr['easemobArr']['member'].$mid),$this->systemArr['easemobmsgArr']['delgroup'].$teamArr->name); //环信

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }


    //退出群组
    public function quitmember(Request $request){
        $teamid = $request->get('teamid','');
        if(!$teamArr = Gteam::where('id','=',$teamid)->first()){
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
        $teamArr = Gteam::where('id','=',$teamid)->first();
        $tmnum = Gteammembers::where('teamid','=',$teamid)->count();
        $tmArr = Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first();
        if(empty($tmArr)){
            return array('error'=>1,'msg'=>'已经不是该群成员');
            exit(); 
        }

        if($tmArr->isleader=='y'){
            if($tmnum==1){
                $res = false;
                DB::beginTransaction();
                    Gteammembers::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete();
                    Gteam::destroy((int)$teamid);
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
      
        if( Gteammembers::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete() ){
            EasemobHelper::delGroupsUser($teamArr->gid,$this->memberArr['easemobArr']['member'].$this->mid);//环信

            return array('error'=>0,'msg'=>'成功'); 
            exit();    
        }
        return array('error'=>1,'msg'=>'失败');
        exit(); 

    }

    //退出比赛群
    private function quitteam_m($teamid){
        $teamArr = Gteam::where('id','=',$teamid)->first();
        
        $tmArr = Gteammembers::where('teamid','=',$teamid)->pluck('mid')->toArray();
        if(empty($tmArr) || !in_array($this->mid,$tmArr)){
            return array('error'=>1,'msg'=>'已经不是该群成员');
            exit(); 
        }

        $tnum = Gteam::where('deletemid','=',$this->mid)->where('gamesagesid','=',$teamArr->gamesagesid)->withTrashed()->count();
        if($tnum>1){ //????????????
            return array('error'=>1,'msg'=>'已经退出多次，系统不允许退出');
            exit(); 
        }
        
        $res = false;
        DB::beginTransaction();
            if(Gteammembers::where(array('teamid'=>$teamid))->count()>7){
                Gteammembers::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete();

                $gaid = $teamArr->gamesagesid;
                $gmArr = Groupmembers::whereHas('group',function($q) use ($gaid){ $q->where('gamesagesid',$gaid); })->where('mid',$this->mid)->first();
                $gmMember = Group::where('id',$gmArr->groupid)->count();
                if($gmMember==1){
                    Group::where('id',$gmArr->groupid)->delete();
                }else{                    
                    Group::where('id','=',$gmArr->groupid)->decrement('number',1);
                }
                Groupmembers::where('id',$gmArr->id)->delete();

                EasemobHelper::delGroupsUser($teamArr->gid,$this->memberArr['easemobArr']['member'].$this->mid);//环信
            }else{
                Gteammembers::where(array('teamid'=>$teamid,'mid'=>$this->mid))->delete();
                $midArr = Gteammembers::where(array('teamid'=>$teamid))->pluck('mid')->toArray();
                $gids = [];
                foreach ($midArr as $k => $v) {
                    $groupArr = Group::where(array('gamesagesid'=>$teamArr->gamesagesid))->whereHas('gmember',function($query) use ($v){
                        $query->select('mid','groupid')->where(array('mid'=>$v));
                    })->first();
                    if(!empty($groupArr)){
                        $gids[$groupArr->id] = $groupArr->id;
                    }
                }
                Group::whereIn('id',$gids)->delete();
                Groupmembers::whereIn('groupid',$gids)->delete();

                Gteam::destroy((int)$teamid);
                Gteammembers::where(array('teamid'=>$teamid))->delete();

                //创建队伍
                $gr = Group::create(['gamesagesid'=>$teamArr->gamesagesid,'number'=>count($midArr),'province'=>$teamArr->province,'city'=>$teamArr->city ]);
                foreach ($midArr as $kmid =>$vmid) {
                    Groupmembers::create(['groupid'=>$gr->id,'mid'=>$vmid,'isleader'=>$kmid==0?'y':'n','status'=>'2']);
                }
                EasemobHelper::delGroup($teamArr->gid);//环信删除群组
            }
            $res = true;  
        DB::commit();
        if($res){
            
            return array('error'=>0,'msg'=>'成功');    
        }
        return array('error'=>1,'msg'=>'失败');
        exit();
    }

    //修改群主
    public function changeleader(Request $request){
        $teamid = $request->get('teamid','');
        $mid = $request->get('changemid','');

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'无权操作'));
            exit(); 
        }

        if(!$teamArr = Gteam::where('id','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'群组不存在'));
            exit(); 
        }

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经不是该群成员'));
            exit(); 
        }

        if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$mid)->first()){
            return response()->json(array('error'=>1,'msg'=>'推荐人已经不是该群成员'));
            exit(); 
        }
      
        $res = false;
        DB::beginTransaction();
            Gteammembers::where(array('teamid'=>$teamid,'mid'=>$this->mid,'isleader'=>'y'))->update(array('isleader'=>'n'));
            Gteammembers::where(array('teamid'=>$teamid,'mid'=>$mid,'isleader'=>'n'))->update(array('isleader'=>'y'));
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
    public function updateicon(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','');

        return response()->json($this->changeteaminfo($teamid,array('icon'=>$val)));
        exit();
    }

    //修改名称
    public function updatename(Request $request){
        $teamid = $request->get('teamid','');
        $val = $request->get('val','');
        if(empty($val)){
            if(mb_strlen($name,'utf8')>6){
                return response()->json(array('error'=>1,'msg'=>'名字不能超过8个字'));
                exit();
            }

            if(Gteam::where('name','=',$name)->where('id','!=',$teamid)->first()){
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
        if(Gteammembers::where('teamid','=',$teamid)->where('mid','!=',$this->mid)->where('number','=',$val)->first() ){
            return response()->json(array('error'=>1,'msg'=>'球衣编号已经有小伙伴使用了')); 
            exit();     
        }  
        return response()->json($this->changeteaminfo($teamid,array('number'=> $val),'m') );
        exit();
    }


    //修改信息
    private function changeteaminfo($teamid,$val,$type='t'){
        if(!$teamArr = Gteam::where('id','=',$teamid)->first()){
            return array('error'=>1,'msg'=>'群组不存在');
            exit(); 
        }

        if($type=='t'){
            if(!Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->where('isleader','=','y')->first()){
                return array('error'=>1,'msg'=>'无权操作');
                exit(); 
            }

            if(Gteam::where('id','=',$teamid)->update($val)){
                return array('error'=>0,'msg'=>'成功');
                exit();     
            }
        }

        if($type=='m'){
            if(Gteammembers::where('teamid','=',$teamid)->where('mid','=',$this->mid)->update($val) ){
                return array('error'=>0,'msg'=>'成功');
                exit();     
            }   
        }

        return array('error'=>1,'msg'=>'失败');
        exit();
    }


    //邀请队员
    public function invite(Request $request){
        $id = $request->get('teamid','');  
        $fmid = $request->get('fmid','');
        if(!$r = Gteammembers::where('teamid',$id)->where('mid',$this->mid)->where('isleader','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'请联系队长进行操作'));
            exit();  
        }

        if($r2 = Gteammembers::where('teamid',$id)->where('mid',$fmid)->first()){
            return response()->json(array('error'=>1,'msg'=>'已经处于队伍中'));
            exit();
        }

        $tArr = Gteam::where(array('id'=>$id,'status'=>'f'))->whereHas('teammember',function($query) use ($fmid){
            $query->select('mid','teamid')->where(array('mid'=>$fmid));
        })->first();
        if(!empty($tArr)){
            return response()->json(array('error'=>1,'msg'=>'已经参赛'));
            exit();
        }

        if(Gteaminvite::where(['gteamid'=>$id,'fmid'=>$fmid,'status'=>'1'])->first()){
            return response()->json(array('error'=>1,'msg'=>'邀请已存在'));
            exit(); 
        }

        if(Gteaminvite::create(['gteamid'=>$id,'mid'=>$this->mid,'fmid'=>$fmid])){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit(); 
        }

        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit();   
    }

     public function invitelist(Request $request){
        $id = $request->get('teamid','');
        $listArr = Gteaminvite::with('fmembers')->where('gteamid',$id)->get();
        $dataArr = [];
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $tmp['icon'] = $v->fmembers->icon;
                $tmp['name'] = $v->fmembers->truename;                
                $tmp['mobile'] = substr($v->fmembers->mobile,0,3).'****'.substr($v->fmembers->mobile,-4);
                $tmp['teamid'] = $v->gteamid;
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
        $id = $request->get('teamid','');
        if($giArr = Gteaminvite::where('gteamid',$id)->where('fmid',$this->mid)->first()){
            $gArr = Gteam::where('id',$id)->first();
            $res = false;   
            if(!empty($gArr)){
                $gmArr = Gteammembers::where('groupid',$id)->count();
                if($gArr->status=='4' || $gmArr>=12){
                    $res = Gteaminvite::where('gteamid',$id)->where('fmid',$this->mid)->update(['status'=>'3']);
                }else{
                    $res = false;            
                    DB::beginTransaction();
                        Gteammembers::create(['teamid'=>$id,'mid'=>$this->mid]);
                        Gteaminvite::where('gteamid',$id)->where('fmid',$this->mid)->update(['status'=>'2']);
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
}
