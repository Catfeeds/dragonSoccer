<?php
namespace App\Http\Controllers\Shell;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use App\Models\Match;
use App\Helpers\EasemobHelper;
use App\Models\Team;
use App\Models\Matchlog;
use Hash;
use DB;
use Config;
class MatchlogController extends Controller
{
    private $teamArr = '';
    private $memberArr = '';
    private $systemArr = '';
    private $matchArr = '';
    private $days = 8;
    private $citygroupmaxteam = 8;
    private $provincegroupmaxteam = 16;
	public function __construct(Request $request){
        $this->teamArr = Config::get('custom.team');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
        $this->matchArr = Config::get('custom.match');
	}


    //获取需要匹配的比赛 报名结束后20时开始匹配
    public function makelog(Request $request){
        //where('endtime','<',time())->where('starttime','>',time())
        $matchArr = Match::where('status','=','y')->orderBy('id', 'desc')->where('applyendtime','>',time()-20*3600)->where('starttime','>',time())->get(['id','teamsts','starttime','endtime']);
        if(!empty($matchArr)){
            foreach ($matchArr as $v) {
                //计算时间
                $i = array_search($v->teamsts,$this->matchArr['matchlevelArr']);
                $j = count($this->matchArr['matchlevelArr']);
                if($i===false || $i==$j){
                    continue;
                }else{
                    $i = $i==0?1:$i;
                    $day = ($i-1)*$this->days;
                    $starttime = $v->starttime+$day+3600*23;
                    $endtime = $v->starttime+$day+3600*24;
                    echo 'makelog matchid id:'.$v->id."\r\n";
                    if(time()>$starttime && time() < $endtime ){ 
                        Redis::lpush('makelog',$v->id);
                        echo 'makelog matchid id:'.$v->id."\r\n";
                    }
                }
            }
        }
    }

    //匹配需要的地区
    public function makeMatchLog(Request $request){
        if($matchid = Redis::rpop('makelog')){
            $matchArr = Match::where('id','=',$matchid)->where('status','=','y')->first();
            if($matchArr->region!='全国' && $matchArr->teamsts=='p4'){
                echo "$matchArr->name---比赛结束--状态---$matchArr->teamsts \r\n";
                return false;
                exit();
            }

            $i = array_search($matchArr->teamsts,$this->matchArr['matchlevelArr']);
            $j = count($this->matchArr['matchlevelArr']);

            if($i!==false || $i!=$j){
                if(in_array($matchArr->teamsts,array('o'))){ //市淘汰赛
                    $areaArr = Team::where(array('matchid'=>$matchid,'sts'=>'s'))->groupBy('city')->get(['city']);
                    if(!empty($areaArr)){
                        foreach ($areaArr as $v) {
                            $pArr = Team::where(array('matchid'=>$matchid,'sts'=>'s','city'=>$v->city))->first(['province']);

                            echo 'may matid id:'.$matchid.'match city:'.$v->city."\r\n";
                            $rlt = $this->makeMatckLogByType($matchid,$v->city,'city');
                            echo "------------\r\n";
                            $r2 = $this->makeloginfo($matchid,$pArr->province,$v->city,$rlt,'c1');
                            echo "赛程匹配完成结束--".($r2?'success':'failed')."\r\n";
                        }
                    }
                }

                if(in_array($matchArr->teamsts,array('c1','c2','p1','p2','p3'))){ //市淘汰赛--同组
                    $snArr = Matchlog::where(array('matchid'=>$matchid,'status'=>'eover','matchlevel'=>$matchArr->teamsts))->where('successteamid','>','0')->groupBy('groupsn')->get(['groupsn']);
                    if(!empty($snArr)){
                        foreach ($snArr as $v) { 
                            $matchlogArr = Matchlog::where(array('matchid'=>$matchid,'status'=>'eover','groupsn'=>$v->groupsn,'matchlevel'=>$matchArr->teamsts))->first(['province','city']);
                            //var_dump($matchlogArr->toArray());
                            $rlt = $this->makeMatckLogBySn($matchid,$v->groupsn,$matchArr->teamsts);
                            //var_dump($rlt);
                            $nextsts = substr($matchArr->teamsts,0,1).(intval(substr($matchArr->teamsts,1))+1);
                            $r2 = $this->makeloginfoSn($matchid,$matchlogArr->province,$matchlogArr->city,$rlt,$nextsts,$v->groupsn);
                            echo "赛程匹配完成结束--".($r2?'success':'failed')."\r\n";
                        }    
                    }
                }

                if(in_array($matchArr->teamsts,array('c3'))){ //省淘汰赛
                    $areaArr = Team::where(array('matchid'=>$matchid,'sts'=>'s'))->groupBy('province')->get(['province']);
                    if(!empty($areaArr)){
                        foreach ($areaArr as $v) {
                            $pArr = Team::where(array('matchid'=>$matchid,'sts'=>'s','province'=>$v->province))->first(['city']);

                            echo 'may matid id:'.$matchid.'match province:'.$v->province."\r\n";
                            $rlt = $this->makeMatckLogByType($matchid,$v->province,'province');
                            echo "------------\r\n";
                            $r2 = $this->makeloginfo($matchid,$v->province,$pArr->city,$rlt,'p1');
                            echo "赛程匹配完成结束--".($r2?'success':'failed')."\r\n";
                        }
                    }
                }

                if(in_array($matchArr->teamsts,array('p4'))){ //全国赛
                   
                }
            }
        }
    }

    //淘汰赛第一轮
    private function makeMatckLogByType($matchid,$cp,$type){
        $groupArr = array();
        $ii = 0;
        $teamArr = array();
        $allnum = 0;  //总数
        $fristnum = 0; //第一组
        $lastnum = 0; //余数
        $avgnum = 0; //平均数
        
        $teamArr = Team::where(array('matchid'=>$matchid,'sts'=>'s',$type=>$cp))->get(['id'])->toArray();
        $allnum = count($teamArr);
        $groupNum = ceil($allnum/$this->citygroupmaxteam);
        $firstnum = ceil($allnum/$groupNum);
        $lastnum = $allnum%$groupNum;
        $avgnum = floor($allnum/$groupNum);
       
        //var_dump($teamArr);
        while ($allnum>0) {
            shuffle($teamArr);
            $num = $ii<$lastnum?$firstnum:$avgnum;
            for($i=0;$i<$num;$i++){             
                $groupArr[$ii][] = $teamArr[$i]['id'];
                unset($teamArr[$i]);
                $allnum--;
            } 
            $ii++; 
        }
        //var_dump($groupArr);
        return $groupArr;
    }

    //同组淘汰赛
    private function makeMatckLogBySn($matchid,$sn,$matchlevel){
        $groupArr = array();
        $ii = 0;
        $teamArr = array();
        $allnum = 0;  //总数
        $fristnum = 2; //第一组
        $avgnum = 2; //平均数
        $teamArr = Matchlog::where(array('matchid'=>$matchid,'status'=>'eover','groupsn'=>$sn,'matchlevel'=>$matchlevel))->where('successteamid','>','0')->get(['successteamid'])->toArray();
        $allnum = count($teamArr);
        if($allnum%2>0){
            $fristnum = 1;
        }

        while ($allnum>0) {
            shuffle($teamArr);
            $num = $ii==0?$fristnum:$avgnum;
            for($i=0;$i<$num;$i++){             
                $groupArr[$ii][] = $teamArr[$i]['successteamid'];
                unset($teamArr[$i]);
                $allnum--;
            } 
            $ii++; 
        }
        return $groupArr;
    }

    private function makeloginfo($matchid,$province,$city,$groupArr,$matchlevel){
        if(!empty($groupArr)){
            $insertArr = array();
            $i = 0;
            foreach ($groupArr as $k => $v) {
                shuffle($v);
                $groupsn = 
                $num = count($v);
                while($num){
                    if($num%2 > 0){   
                           
                        $insertArr[$i]['ateamid'] = $v[$num-1];       
                        $insertArr[$i]['bteamid'] = '0';         
                        $insertArr[$i]['status'] = 'eover';
                        $insertArr[$i]['successteamid'] = $v[$num-1];
                        $num--;    
                    }else{  
                        $insertArr[$i]['ateamid'] = $v[$num-1];       
                        $insertArr[$i]['bteamid'] = $v[$num-2];         
                        $insertArr[$i]['status'] = 'mw'; 
                        $num-=2;
                    }
                    $insertArr[$i]['groupsn'] = $city.substr($matchlevel,0,1).$k; 
                    $insertArr[$i]['matchid'] = $matchid;     
                    $insertArr[$i]['ateamscore'] = 0;      
                    $insertArr[$i]['bteamscore'] = 0;    
                    $insertArr[$i]['matchlevel'] = $matchlevel;   
                    $insertArr[$i]['province'] = $province;    
                    $insertArr[$i]['city'] = $city;

                    $i++;    
                }
            }                
        }

        $res = false;
        if(!empty($insertArr)){
            DB::beginTransaction();
                foreach ($insertArr as $k => $v){
                    if($v['status']=='eover'){
                        Team::where(array('id'=>$v['ateamid']))->update(array('sts'=>'s','level'=>$v['matchlevel']));
                    }else{
                        Team::where(array('id'=>$v['ateamid']))->update(array('sts'=>'w','level'=>$v['matchlevel']));
                        Team::where(array('id'=>$v['bteamid']))->update(array('sts'=>'w','level'=>$v['matchlevel']));
                    }
                    $r = Matchlog::create($v);
                    echo "match city:".$v['city']."groupsn:".$v['groupsn'].($r?'success':'failed')."\r\n";
                } 

                Match::where(['id'=>$matchid])->update(['teamsts'=>$matchlevel]);             
                $res = true;  
            DB::commit();
        }

        return $res;
    }


    private function makeloginfosn($matchid,$province,$city,$groupArr,$matchlevel,$groupsn){
        if(!empty($groupArr)){
            $insertArr = array();
            foreach ($groupArr as $k => $v) {                
                if(count($v)==1){
                    $insertArr[$k]['ateamid'] = $v[0];       
                    $insertArr[$k]['bteamid'] = '0';         
                    $insertArr[$k]['status'] = 'eover';
                    $insertArr[$k]['successteamid'] = $v[0];
                }else{
                    $insertArr[$k]['ateamid'] = $v[0];       
                    $insertArr[$k]['bteamid'] = $v[1];         
                    $insertArr[$k]['status'] = 'mw';     
                }

                $insertArr[$k]['groupsn'] = $groupsn; 
                $insertArr[$k]['matchid'] = $matchid;     
                $insertArr[$k]['ateamscore'] = 0;      
                $insertArr[$k]['bteamscore'] = 0;    
                $insertArr[$k]['matchlevel'] = $matchlevel;   
                $insertArr[$k]['province'] = $province;    
                $insertArr[$k]['city'] = $city;
            }                
        }

        $res = false;
        if(!empty($insertArr)){
            DB::beginTransaction();
                foreach ($insertArr as $k => $v){
                    if($v['status']=='eover'){
                        Team::where(array('id'=>$v['ateamid']))->update(array('sts'=>'s','level'=>$v['matchlevel']));
                    }else{
                        Team::where(array('id'=>$v['ateamid']))->update(array('sts'=>'w','level'=>$v['matchlevel']));
                        Team::where(array('id'=>$v['bteamid']))->update(array('sts'=>'w','level'=>$v['matchlevel']));
                    }
                    $r = Matchlog::create($v);
                    echo "match city:".$v['city']."groupsn:".$v['groupsn'].($r?'success':'failed')."\r\n";
                } 

                Match::where(['id'=>$matchid])->update(['teamsts'=>$matchlevel]);             
                $res = true;  
            DB::commit();
        }

        return $res;
    }

    
}