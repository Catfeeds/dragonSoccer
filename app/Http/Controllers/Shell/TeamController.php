<?php
namespace App\Http\Controllers\Shell;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use App\Models\Match;
use App\Models\Relation;
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
    private $teamArr = '';
    private $memberArr = '';
    private $systemArr = '';
    private $matchArr = '';
    private $position = ['f','m','b','gk'];
    private $num = ['f'=>'3','m'=>'2','b'=>'2','gk'=>'2'];
	public function __construct(Request $request){
        $this->teamArr = Config::get('custom.team');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
        $this->matchArr = Config::get('custom.match');
	}


    //获取需要匹配的比赛  报名结束后0-18点
    public function maybeMatch(Request $request){
        $matchArr = Match::where('teamsts','=','w')->where('status','=','y')->where('applyendtime','>',time()-18*3600)->where('applyendtime','<',time())->where('starttime','>',time())->orderBy('id', 'desc')->get(['id']);
        //var_dump($matchArr->toArray());
        if(!empty($matchArr)){
            foreach ($matchArr as $v) {
                Redis::lpush('maymatch',$v->id);
                echo 'may matid id:'.$v->id."\r\n";
            }
        }
    }

    //匹配需要的地区
    public function maybeMatchArea(Request $request){
        if($matchid = Redis::rpop('maymatch')){
            $areaArr = Apply::where(array('matchid'=>$matchid))->whereIn('status',array('6','7'))->groupBy('city')->get(['city']);
            if(!empty($areaArr)){
                Redis::lpush('maymatchareaid',$matchid);
                foreach ($areaArr as $v) {
                    Redis::lpush('maymatchareacity'.$matchid,$v->city);
                    echo 'may matid id:'.$matchid.'match city:'.$v->city."\r\n";
                }
            }
        }
    }

    //人员位置-主位置
    public function maybeMatchPosition(Request $request){
        if($matchid = Redis::rpop('maymatchareaid')){
            if($city = Redis::rpop('maymatchareacity'.$matchid)){
                Redis::lpush('maymatchpositionid',$matchid);
                Redis::lpush('maymatchpositioncity'.$matchid,$city);

                $redismid = Redis::get('twoMatchMid');
                $positionArr = array();
                $p = 0;
                foreach ($this->position as $v) {
                    $positionArr = Apply::where(array('matchid'=>$matchid,'city'=>$city,'position'=>$v))->where('mid','>',empty($redismid)?0:$redismid)->where('friend_mid','>','0')->whereIn('status',array('6','7'))->limit(10)->get(['mid']);

                    if(!empty($positionArr->toArray())){
                        $p = 1;
                        foreach ($positionArr as $vv) {
                            Redis::set('twoMatchMid',$vv->mid);
                            Redis::lpush('maymatchposition'.$matchid.md5($city),$vv->mid);
                            echo 'maymatchposition ---may matid id:'.$matchid.'match city:'.$city."mid : $vv->mid  \r\n";
                        }
                    }
                }
                if(empty($p)){
                    Redis::set('twoMatchMid',0);
                }

                //单身
                $redismid2 = Redis::get('twoMatchMid2');
                $positionArr2 = array();
                $p2 = 0;
                foreach ($this->position as $v) {
                    $positionArr2 = Apply::where(array('matchid'=>$matchid,'city'=>$city,'position'=>$v))->where('mid','>',empty($redismid2)?0:$redismid2)->where('friend_mid','=','0')->whereIn('status',array('6','7'))->limit(10)->get(['mid','position']);

                    if(!empty($positionArr2->toArray())){
                        $p2 = 1;
                        foreach ($positionArr2 as $vv) {
                            Redis::set('twoMatchMid2',$vv->mid);
                            if( !Apply::where(array('matchid'=>$matchid,'city'=>$city))->where('friend_mid','=',$vv->mid)->whereIn('status',array('6','7'))->first()){
                                Redis::lpush('maymatchposition2'.$matchid.md5($city).$vv->position,$vv->mid);
                                echo 'maymatchposition2 ---may matid id:'.$matchid.'match city:'.$city."position:$vv->position   mid : $vv->mid \r\n";
                            }
                        }    
                    }
                }
                if(empty($p2)){
                    Redis::set('twoMatchMid2',0);
                }
            }
        }
        echo "----------------------\r\n";
    }

    
    public function maketeam(Request $request){
        if($matchid = Redis::rpop('maymatchpositionid')){
            if($city = Redis::rpop('maymatchpositioncity'.$matchid)){
                //var_dump(Redis::lrange('maymatchposition'.$matchid.md5($city),0,-1));
                if(Redis::lrange('maymatchposition'.$matchid.md5($city),0,-1)){  //双排
                    $positionArr = $this->getalltwo($matchid,$city);
                    if(!empty($positionArr)){
                        foreach ($this->position as $v) {
                            $inum = empty($positionArr[$v])?0:count($positionArr[$v]);
                            while ($inum < $this->num[$v] && $mid = Redis::rpop('maymatchposition2'.$matchid.md5($city).$v) ) {
                                echo "---$inum  --- $v----$mid \r\n";
                                $midArr = Apply::where(array('matchid'=>$matchid,'city'=>$city,'mid'=>$mid))->whereIn('status',array('6','7'))->first();
                                if(!empty($midArr) && $midArr->position==$v ){
                                    if(empty($positionArr[$v]) ||(!empty($midArr) && !in_array($mid,$positionArr[$v])) ){
                                        $positionArr[$v][] = $mid;
                                        $inum++;
                                    } 
                                }
                            }
                        }
                    }        
                }else{ //单排
                    $positionArr = $this->getallone($matchid,$city);                   
                }

                if(!empty($positionArr)){
                    //var_dump($positionArr);
                    $r = $this->createteam($matchid,$city,$positionArr);
                    echo "组队".($r?'成功 success':'失败 failed')."\r\n";
                }
            }
        }
    }

    private function getalltwo($matchid,$city){
        $positionArr = array();

        $ii = 0;
        while ($ii<8 && $mid = Redis::rpop('maymatchposition'.$matchid.md5($city)) ) {
            echo "mid ==== $mid \r\n";
            $ii = 0;
            foreach ($this->position as $v) {
                $ii += empty($positionArr[$v])?0:count($positionArr[$v]);           
            }
            //var_dump($ii);
            $fmidArr = array();
            if($midArr = Apply::where(array('matchid'=>$matchid,'city'=>$city,'mid'=>$mid))->whereIn('status',array('6','7'))->first()){
                $fmidArr = Apply::where(array('matchid'=>$matchid,'city'=>$city,'mid'=>$midArr->friend_mid))->whereIn('status',array('6','7'))->first();
            }

            if(!empty($midArr) && !empty($fmidArr)){
                if($midArr->position == $fmidArr->position){
                    $i = empty($positionArr[$midArr->position])?0:count($positionArr[$midArr->position]);
                    if($i<$this->num[$midArr->position]-1){
                        if(empty($positionArr[$midArr->position]) || !in_array($midArr->mid,$positionArr[$midArr->position]) ){
                            if(empty($positionArr[$fmidArr->position]) || !in_array($fmidArr->mid,$positionArr[$fmidArr->position]) ){
                                $positionArr[$midArr->position][] =  $midArr->mid;    
                                $positionArr[$fmidArr->position][] =  $fmidArr->mid;  
                            }  
                        }     
                    } 
                }else{
                    $i = empty($positionArr[$midArr->position])?0:count($positionArr[$midArr->position]);
                    $j = empty($positionArr[$fmidArr->position])?0:count($positionArr[$fmidArr->position]);

                    if($i<$this->num[$midArr->position] && $j<$this->num[$fmidArr->position]){
                        if(empty($positionArr[$midArr->position]) || !in_array($midArr->mid,$positionArr[$midArr->position]) ){
                            if(empty($positionArr[$fmidArr->position]) || !in_array($fmidArr->mid,$positionArr[$fmidArr->position]) ){
                                $positionArr[$midArr->position][] =  $midArr->mid;    
                                $positionArr[$fmidArr->position][] =  $fmidArr->mid;  
                            }  
                        }  
                    }
                }
            }else{
                continue;
            }
        }

        return $positionArr;
    }

    private function getallone($matchid,$city){
        $positionArr = array();
        foreach ($this->position as $v) {
            $i=0;
            while($i<$this->num[$v] && $mid = Redis::rpop('maymatchposition2'.$matchid.md5($city).$v)){
                if($midArr = Apply::with('member')->where(array('mid'=>$mid,'matchid'=>$matchid,'city'=>$city))->whereIn('status',array('6','7'))->first() ){
                    if($midArr->position==$v ){
                        if(empty($positionArr[$v]) || !in_array($mid,$positionArr[$v])){
                            $positionArr[$v][] = $mid;  
                            $i++;     
                        }
                    }
                }
            }                
        }
        //var_dump($positionArr);
        return $positionArr;
    }

    //生成群组
    private function createteam($matchid,$city,$positionArr){
        $res = false; 
        if(!empty($positionArr)){
            foreach ($this->num as $kg => $vg) {
                if(empty($positionArr[$kg]) || count($positionArr[$kg])!= $vg){
                    return false;
                }
            }

            //生成群组
            DB::beginTransaction();
                $matchArr = Match::where('id','=',$matchid)->where('status','=','y')->orderBy('id', 'desc')->first();
                $insertMemArr = array();

                $j = 0;
                $ownid = '';
                $province = '';
                foreach ($positionArr as $groupkey => $mids) {
                    foreach ($mids as $mid) {
                        echo "mid----- $mid \r\n";
                        $applyArr = Apply::with('member')->where(array('matchid'=>$matchid,'mid'=>$mid))->whereIn('status',array('6','7'))->first();
                        if(empty($applyArr)){
                            return false;
                        }
                        $insertMemArr[$j]['mid'] = $mid;   
                        $insertMemArr[$j]['matchid'] = $matchid;   
                        $insertMemArr[$j]['name'] = $applyArr->member->name;
                        $insertMemArr[$j]['isleader'] = $j==0?'y':'n';
                        $easemobGids[] = $this->memberArr['easemobArr']['member'].$mid; 
                        if($j==0){
                            $ownid = $this->memberArr['easemobArr']['member'].$mid;
                            $province = $applyArr->province;
                        }
                        $j++;     
                    } 
                }
                    
                //$teamname = $matchArr->name.'-'.$city.date('dHi').rand();
                $teamname = '龙少'.rand(1000,9999);
                echo "---------队伍名称------$teamname"."\r\n";
                $teamMemberName = '';
                if($teamrlt = Team::create(array('name'=>$teamname,'matchid'=>$matchid,'province'=>$province,'city'=>$city,'sts'=>'s','level'=>'o','type'=>'m'))){
                    foreach ($insertMemArr as $k => $v) {
                        $v['teamid'] = $teamrlt->id;
                        Teammember::create($v);
                        $teamMemberName .= $v['name']."|" ;
                        Apply::where(array('matchid'=>$matchid,'mid'=>$v['mid']))->update(array('status'=>'8'));
                    }                    

                    //环信
                    $gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$teamrlt->id,$teamname,$ownid,$easemobGids);//环信
                    if(!empty($gid)){
                        if(Team::where(array('id'=>$teamrlt->id))->update(array('gid'=>$gid))){
                            //系统消息
                            EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
                            EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信

                            $r = EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],array($gid),$teamMemberName,$this->systemArr['easemobtypeArr']['group']); //环信 
                        }   
                    }
                }                
                $res = true;  
            DB::commit();
        }

        return $res;
    }
 
    //匹配完成 报名结束后10-11
    public function makeMatchOver(Request $request){
        $matchArr = Match::where('teamsts','=','w')->where('applyendtime','>',time()-11*3600)->where('applyendtime','<',time()-10*3600)->orderBy('id', 'desc')->get(['id']);
        if(!empty($matchArr)){
            foreach ($matchArr as $v) {
                echo "匹配完成--id:$v\r\n";
                $allnum = Apply::where(array('matchid'=>$v->id))->whereIn('status',array('6','7'))->count();
                if($allnum>0){
                    foreach ($this->position as $p) {
                        $pnum = Apply::where(array('matchid'=>$v->id,'position'=>$p))->whereIn('status',array('6','7'))->count();
                        if($pnum < $this->num[$p]){
                            $r = Match::where(array('id'=>$v->id))->update(array('teamsts'=>'s'));
                            echo "某个位置组队缺少人员组队失败--".($r?'success':'failed')."\r\n";
                            break;
                        }else{
                            continue;
                        }        
                    }    
                }else{
                    $r = Match::where(array('id'=>$v->id))->update(array('teamsts'=>'s'));
                    echo "全部组队--".($r?'success':'failed')."\r\n";
                }
                
            }
        }
    }

    //匹配失败 结束报名16-18
    public function makeMatchFailed(Request $request){ 
        $matchArr = Match::where('applyendtime','>',time()-24*3600)->where('applyendtime','<',time()-16*3600)->orderBy('id', 'desc')->get(['id','teamsts','applyendtime']);
        //var_dump($matchArr->toArray());
        if(!empty($matchArr)){
            foreach ($matchArr as $v) {
                if($v->teamsts == 'f' || ($v->applyendtime > time()-18*3600) ){
                    $r = Apply::where(array('matchid'=>$v->id))->whereIn('status',array('6','7'))->update(array('status'=>'3'));
                    echo ($v->teamsts == 'f'?"按照后台指示，修改状态为匹配失败--":"超时，修改状态为匹配失败--").($r?'success':'failed')."\r\n";
                    $r2 = Match::where(array('id'=>$v->id))->update(array('teamsts'=>'o','remark'=>date('y-m-d H:i:s').'脚本匹配失败' ));
                    echo "组队结束--".($r2?'success':'failed')."\r\n";
                }
            }
        }
    }
    
}