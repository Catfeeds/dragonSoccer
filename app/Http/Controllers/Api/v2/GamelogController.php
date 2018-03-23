<?php
namespace App\Http\Controllers\Api\v2;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\FunctionHelper;
use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Gamecontent;
use App\Models\School;
use App\Models\Gamelog;
use App\Models\Gteam;
use App\Models\Gteammembers;
use App\Models\Vote;
use App\Models\Matchlogsetting;
use App\Models\Matchlogcontent;
use Config;
use DB;
class GamelogController extends Controller{
	    
	//private $statusArr = ['w'=>'等待比赛','r'=>'即将开始','go'=>'开始','st'=>'暂停比赛','e'=>'比赛结束'];
    private $statusmsgArr = ['m'=>'即将开始','e'=>'已结束'];
    private $statusArr = ['mw'=>'待定','mc'=>'待审核','mwate'=>'准备','mready'=>'即将开始','mgo'=>'开始','end'=>'比赛结束','eupc'=>'结果审核','eover'=>'结束'];
    private $mid = '';
    public function __construct(Request $request){
        $this->mid = $request->get('mid','');
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
    	$query = Gamelog::where('gamesagesid','=',$id);				
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $glArr = $query->select(['id','groupsn','gamesagesid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->get();

        $dataArr = [];
        $timeArr = [];
		if($glArr){
			foreach ($glArr as $k => $v) {
				$tmp['logid'] = $v->id;
	        	$tmp['ateamid'] = empty($v->ateam)?'':$v->ateamid;
	        	$tmp['ateamname'] = empty($v->ateam)?$v->ateamid:$v->ateam->name;
	        	$tmp['ateamicon'] = empty($v->ateam)?'':$v->ateam->icon;
	        	$tmp['ateamscore'] = $v->ateamscore;
	        	$tmp['bteamid'] = empty($v->bteam)?'':$v->bteamid;
	        	$tmp['bteamname'] = empty($v->bteam)?$v->bteamid:$v->bteam->name;
	        	$tmp['bteamicon'] = empty($v->bteam)?'':$v->bteam->icon;
	        	$tmp['bteamscore'] = $v->bteamscore;
	        	$tmp['status'] = $v->status;
	        	$tmp['statusmsg'] = $this->statusArr[$v->status];

	        	$timeArr[$v->groupsn][] = $v->stime;
	        	$dataArr[$v->groupsn]['lists'][] = $tmp;
			}			
        }

        if(!empty($dataArr)){
        	foreach ($dataArr as $k => &$v) {
        		if(!empty($timeArr[$k])){
					asort($timeArr[$k]);
					$v['time'] = date('Y年m月d日',$timeArr[$k][0]).'-'.date('Y年m月d日',$timeArr[$k][count($timeArr[$k])-1]);
					if(count($timeArr)==1){
						$v['time'] = date('Y年m月d日',$timeArr[$k][0]);
					}				
				}
				$v['title'] = $k;	
        	}
        }

    	return array_values($dataArr);    
    }

    public function getall(Request $request){
        $dataArr = array();
        $teamArr = Gteammembers::where('mid','=',$this->mid)->get(['teamid','isleader']);
        if(!empty($teamArr)){
            foreach ($teamArr as $vt) {
                $tid = $vt->teamid;
                $tArr = Gteam::where('id','=',$tid)->first();                
                //比赛详情
                $query = Gamelog::select(['id','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city','created_at','updated_at']);               
                $query = $query->where(function ($query) use ($tid) {
                    $query->orWhere('ateamid','=',$tid)->orWhere('bteamid','=',$tid);
                });
                $dataArr2 = $query->get();
                //$tmpArr = array();
                foreach ($dataArr2 as $k => $v) {
                    $key = '';
                    $keymsg = '';
                    if(substr($v->matchlevel,0,1)=='1'){
                        $keymsg = '市预选赛';
                    }

                    if(substr($v->matchlevel,0,1)=='2'){
                        $keymsg = '省淘汰赛';    
                    }

                    if(substr($v->matchlevel,0,1)=='3'){
                        $keymsg = '全国总决赛';   
                    }

                    $arr = array();
                    $arr['title'] = $keymsg;
                    $arr['gamelogsn'] = date('ymdHi',strtotime($v->created_at)).'#'.$v->id;
                    $arr['gamelogid'] = $v->id;
                    $arr['teamid'] = $tArr->id;
                    $arr['teamname'] = $tArr->name;
                    $arr['teamicon'] = $tArr->icon;

                    $arr['stime'] = empty($v->stime)?'--':date('Y-m-d');
                    $arr['address'] = empty($v->address)?'':$v->address;

                    $arr['status'] = $v->status;
                    if($v->status=='mready'){ //有人点击比赛 11-17
                        $mlogcontentArr = Matchlogcontent::where(array('matchlogid'=>$v->id,'mid'=>$this->mid,'teamid'=>$tid))->first();
                        $arr['status'] = empty($mlogcontentArr)?'mwate':'mready';   
                    }

                    $arr['isleader'] = $vt->isleader;

                    $vArr = Vote::where(array('matchlogid'=>$v->id,'mid'=>$this->mid))->first();
                    $arr['isbest'] = empty($vArr)?false:true;

                    $arr['percent'] = 0.00;
                    if($v->status == 'mgo'){
                        $gotime = time() - strtotime($v->updated_at);
                        $arr['percent'] = ceil($gotime/60/70*10000)/100;
                    }

                    $arr['bteamname'] = ''; 
                    if($v->ateamid==$tArr->id){
                        if(!empty($v->bteamid)){
                            if($btArr = Gteam::where('id','=',$v->bteamid)->first()){
                                $arr['bteamname'] = $btArr->name;
                            }
                        }
                    }
                    if($v->bteamid==$tArr->id){
                        if(!empty($v->ateamid)){
                            if($atArr = Gteam::where('id','=',$v->ateamid)->first()){
                                $arr['bteamname'] = $atArr->name;
                            }
                        }
                    }
                    
                    $dataArr[substr($v->status,0,1)]['content'][] = $arr; 
                    $dataArr[substr($v->status,0,1)]['title'] = $this->statusmsgArr[substr($v->status,0,1)]; 
                }
                //$dataArr[$tid] = array_values($tmpArr);
            }
        }
        empty($dataArr)?'':krsort($dataArr);
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array_values($dataArr) ));
        exit();
    }

    //今日赛事
    public function day(Request $request){
        $time = strtotime(date('Y-m-d'));
        $query = Gamelog::where('stime','>=',$time)->where('stime','<=',$time+3600*24);
        //$query = new Gamelog();
        $query = $query->with(array('gamesages'=>function ($query){
                $query->select('id','gamesid','val');
            })); 
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $listArr = $query->orderBy('id', 'desc')->paginate(20);

        $dataArr = array('number'=>empty($listArr)?0:$listArr->total(),'info'=>array());
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {                
                $key = '';
                if(substr($v->matchlevel,0,1)=='1'){
                    $key = $v->city; 
                }

                if(substr($v->matchlevel,0,1)=='2'){
                    $key = $v->province;     
                }

                if(substr($v->matchlevel,0,1)=='3'){
                    $key = '全国';     
                }

                $arr = array();
                $status = '即将开始';
                if($v->status=='eover'){
                    $status = $v->ateamscore.':'.$v->bteamscore;
                }else{
                    $status = $this->statusArr[$v->status];
                }

                $arr['gamelogid'] = $v->id;       
                $arr['gameid'] = $v->gamesages->gamesid;       
                $arr['type'] = 's';       
                $arr['region']    = $key.'赛区';    
                $arr['gameval']    = $v->gamesages->val;    
                $arr['aimg']   = empty($v->ateam)?'':$v->ateam->icon; //主场 像
                $arr['aname']  = empty($v->ateam)?$v->ateamid:$v->ateam->name;//主场名称 
                $arr['bimg']   = empty($v->bteam)?'':$v->bteam->icon ; //主场 像
                $arr['bname']  = empty($v->bteam)?$v->ateamid:$v->bteam->name;//客场名称   
                $arr['statusmsg'] = $status;//状态 

                $dataArr['info'][] = $arr;
            }
        }
        

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr ));
        exit();
    }


    //比赛日期地址选择
    public function chosedateaddress(Request $request){
        $gamelogid = $request->get('gamelogid','');   
        $teamid = $request->get('teamid','');
        $dataArr = array('mtime'=>array(),'rname'=>'','phone'=>'','address'=>'','matchlogid'=>$gamelogid,'teamid'=>$teamid);
        if($setArr = Matchlogsetting::where(array('matchlogid'=>$gamelogid,'teamid'=>$teamid,'mid'=>$this->mid))->first() ){
            $dataArr['mtime']= empty($setArr->mtime)?array():json_decode($setArr->mtime);
            $dataArr['rname']= empty($setArr->rname)?'':$setArr->rname;
            $dataArr['phone']= empty($setArr->phone)?'':$setArr->phone;
            $dataArr['address']= empty($setArr->address)?'':$setArr->address;   
        }
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit(); 
    }

    //比赛日期地址选择 修改
    public function savechosedateaddress(Request $request){ 
        $mtime = $request->get('mtime',''); 
        $rname = $request->get('rname',''); 
        $phone = $request->get('phone',''); 
        $address = $request->get('address','');

        $gamelogid = $request->get('gamelogid','');   
        $teamid = $request->get('teamid','');

        if(gettype($mtime)!='array'){
            return response()->json(array('error'=>1,'msg'=>'参数错误'));
            exit();
        }

        if(empty($address)){
            return response()->json(array('error'=>1,'msg'=>'请填写地址'));
            exit();
        }

        $dataArr = array('mtime'=>json_encode($mtime),'rname'=>$rname,'phone'=>$phone,'address'=>$address,'matchlogid'=>$gamelogid,'teamid'=>$teamid,'mid'=>$this->mid);
        $r = false;

        $rlt = Matchlogsetting::where(array('matchlogid'=>$gamelogid,'teamid'=>$teamid,'mid'=>$this->mid))->first();
        if(!empty($rlt) ){
            $r = Matchlogsetting::where('id','=',$rlt->id)->update($dataArr);   
        }else{
            $r = Matchlogsetting::create($dataArr); 
        }

        if($r){
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }   
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit(); 
    }


    //比赛结果填写
    public function saveresult(Request $request){   
        $gamelogid = $request->get('gamelogid','');   
        $teamid = $request->get('teamid','');
        $type = $request->get('type','');
        $val = $request->get('val',''); 
        $valt = $request->get('valt','');   

        if(empty($gamelogid)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['matchlogid']= $gamelogid;    

        if(empty($teamid)){
            return response()->json(array('error'=>1,'msg'=>'队伍id不能为空'));
            exit();
        }
        $dataArr['teamid']= $teamid;

        if(empty($type)){
            return response()->json(array('error'=>1,'msg'=>'类型不能为空'));
            exit();
        }
        $dataArr['type']= $type;

        if(in_array($type,array('c','d','e'))){
            if(empty($val)){
                return response()->json(array('error'=>1,'msg'=>'参数不能为空'));
                exit();
            }
            $dataArr['txt1']= $val;
        }
        

        if($type=='a'){
            if(empty($val) || empty($valt)){
                return response()->json(array('error'=>1,'msg'=>'分数不能为空'));
                exit();
            }
            $dataArr['txt1']= $val;
            $dataArr['txt2']= $valt;
        }
        

        if($type=='b'){
            if(empty($val) && (gettype($valt)!='array' || count($valt)<1) ){
                return response()->json(array('error'=>1,'msg'=>'文字或图片不能为空'));
                exit();
            }
            $dataArr['txt1']= $val;
            $dataArr['imgs']= json_encode($valt);
        }

        if(in_array($type,array('a','d','e'))){
            if(!$teamArr = Gteammembers::where('mid','=',$this->mid)->where('isleader','=','y')->where('teamid','=',$teamid)->first()){
                return response()->json(array('error'=>1,'msg'=>'无权操作'));
                exit();
            }   
        }

        if($type != 'b'){
            if(Matchlogcontent::where(array('matchlogid'=>$gamelogid,'teamid'=>$teamid,'mid'=>$this->mid,'type'=>$type))->first()){
                return response()->json(array('error'=>1,'msg'=>'已经填写'));
                exit(); 
            }               
        }

        $dataArr['mid']= $this->mid;
        $res = false;
        DB::beginTransaction();
            Matchlogcontent::create($dataArr);
            if(in_array($type,array('a'))){             
                Gamelog::where('id','=',$gamelogid)->update(array('status'=>'eupc'));
            }

            if(in_array($type,array('d','e'))){             
                Gamelog::where('id','=',$gamelogid)->update(array('status'=>'end'));
            }
            $res = true;  
        DB::commit();
        
        if($res){           
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }   
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit(); 
    }


    //开始比赛
    public function startmatchlog(Request $request){    
        $gamelogid = $request->get('gamelogid','');   
        $teamid = $request->get('teamid','');

        $mlogArr = Gamelog::select(['id','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city','created_at'])->where('id','=',$gamelogid)->first();
        if(empty($gamelogid) || empty($mlogArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['gamelogid']= $gamelogid;    

        if(empty($teamid)){
            return response()->json(array('error'=>1,'msg'=>'队伍id不能为空'));
            exit();
        }
        $dataArr['teamid']= $teamid;

        if(!$teamArr = Gteammembers::where('mid','=',$this->mid)->where('isleader','=','y')->first()){
            return response()->json(array('error'=>1,'msg'=>'无权操作'));
            exit();
        }

        $res = false;
        if($mlogArr->status=='mwate'){ //添加开始比赛
            $dataArr['type']= 'f'; //开始比赛
            $dataArr['txt1']= 'w'; //等待对方确认
            $dataArr['mid']= $this->mid; //等待对方确认
            DB::beginTransaction();
                Matchlogcontent::create($dataArr);
                Gamelog::where('id','=',$gamelogid)->update(['status'=>'mready']);
                $res = true;  
            DB::commit();
        }elseif($mlogArr->status=='mready'){ //添加开始比赛
            $mlogcontentArr = Matchlogcontent::where(array('matchlogid'=>$gamelogid,'type'=>'f'))->first();
            if($mlogcontentArr->mid == $this->mid || $mlogcontentArr->teamid == $teamid ){ //自己点的
                return response()->json(array('error'=>1,'msg'=>'已经请求，请勿重复点击'));
                exit();
            }else{ //对方点的
                DB::beginTransaction();
                    Matchlogcontent::where(array('id'=>$mlogcontentArr->id))->update(['txt1'=>'s']);
                    Gamelog::where('id','=',$gamelogid)->update(['status'=>'mgo']);
                    $res = true;  
                DB::commit();
            }
        }

        if($res){           
            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }   
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit(); 
    }
    
    public function votesave(Request $request){ 
        $gamelogid = $request->get('gamelogid','');   
        $teamid = $request->get('teamid','');
        $bestmid = $request->get('bestmid','');

        if(empty($gamelogid)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['matchlogid']= $gamelogid;    

        if(empty($teamid)){
            return response()->json(array('error'=>1,'msg'=>'队伍id不能为空'));
            exit();
        }
        $dataArr['teamid']= $teamid;

        if(empty($bestmid) || $bestmid==$this->mid){
            return response()->json(array('error'=>1,'msg'=>'请选择最佳小伙伴'));
            exit();
        }
        $dataArr['bestmid']= $bestmid;

        if(!$teamArr = Gteammembers::where('mid','=',$bestmid)->where('teamid','=',$teamid)->first()){
            return response()->json(array('error'=>1,'msg'=>'只能推荐自己队伍的小伙伴'));
            exit();
        }   

        if(Vote::where(array('matchlogid'=>$gamelogid,'mid'=>$this->mid))->first()){
            return response()->json(array('error'=>1,'msg'=>'已使用过推荐最佳小伙伴机会'));
            exit();
        }
        
        $dataArr['mid']= $this->mid;
        //var_dump($dataArr);
        if($r = Vote::create($dataArr)){

            return response()->json(array('error'=>0,'msg'=>'成功'));
            exit();     
        }   
        return response()->json(array('error'=>1,'msg'=>'失败'));
        exit(); 
    }



}
