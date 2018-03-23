<?php
namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use App\Models\Match;
use App\Helpers\EasemobHelper;
use App\Models\Team;
use App\Models\Teammember;
use App\Models\Matchlog;
use App\Models\Matchlogsetting;
use App\Models\Matchlogcontent;
use App\Models\Vote;
use Hash;
use DB;
use Config;
class MymatchlogController extends Controller
{
	private $teamArr = '';
	private $memberArr = '';
	private $systemArr = '';
	private $matchArr = '';
	private $matchlogArr = '';

	private $mid = '';
	public function __construct(Request $request){
		$this->teamArr = Config::get('custom.team');
		$this->memberArr = Config::get('custom.member');
		$this->systemArr = Config::get('custom.system');
		$this->matchArr = Config::get('custom.match');
		$this->matchlogArr = Config::get('custom.matchlog');

		$this->mid = $request->get('mid','');
	}
	

	//我的赛程
	public function alist(Request $request){
		$dataArr = array();
		$teamArr = Teammember::where('mid','=',$this->mid)->get(['teamid','isleader']);
		if(!empty($teamArr)){
			foreach ($teamArr as $vt) {
				$tid = $vt->teamid;
				$tArr = Team::where('id','=',$tid)->first(); 				
				//比赛详情
				$query = Matchlog::select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city','created_at','updated_at']);				
				$query = $query->where(function ($query) use ($tid) {
		            $query->orWhere('ateamid','=',$tid)->orWhere('bteamid','=',$tid);
		        });
		        $dataArr2 = $query->get();
		        //$tmpArr = array();
		        foreach ($dataArr2 as $k => $v) {
		        	$key = '';
		        	$keymsg = '';
		        	if(substr($v->matchlevel,0,1)=='c'){
		        		$keymsg = '市预选赛';
		        	}

		        	if(substr($v->matchlevel,0,1)=='p'){
		        		$keymsg = '省淘汰赛'; 	 
		        	}

		        	if(substr($v->matchlevel,0,1)=='t'){
		        		$keymsg = '全国总决赛'; 	 
		        	}

		        	$arr = array();
		        	$arr['title'] = $keymsg;
		        	$arr['matchlogsn'] = date('ymdHi',strtotime($v->created_at)).'#'.$v->id;
		        	$arr['matchlogid'] = $v->id;
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
		        			if($btArr = Team::where('id','=',$v->bteamid)->first()){
		        				$arr['bteamname'] = $btArr->name;
		        			}
		        		}
		        	}
		        	if($v->bteamid==$tArr->id){
		        		if(!empty($v->ateamid)){
		        			if($atArr = Team::where('id','=',$v->ateamid)->first()){
		        				$arr['bteamname'] = $atArr->name;
		        			}
		        		}
		        	}
		        	
		        	$dataArr[substr($v->status,0,1)]['content'][] = $arr; 
		        	$dataArr[substr($v->status,0,1)]['title'] = $this->matchlogArr['statusmsgArr'][substr($v->status,0,1)]; 
		        }
				//$dataArr[$tid] = array_values($tmpArr);
			}
		}
		empty($dataArr)?'':krsort($dataArr);
		return response()->json(array('error'=>0,'msg'=>'成功','data'=>array_values($dataArr) ));
        exit();
	}

	//比赛日期地址选择
	public function chosedateaddress(Request $request){
		$matchlogid = $request->get('matchlogid','');	
		$teamid = $request->get('teamid','');
		$dataArr = array('mtime'=>array(),'rname'=>'','phone'=>'','address'=>'','matchlogid'=>$matchlogid,'teamid'=>$teamid);
		if($setArr = Matchlogsetting::where(array('matchlogid'=>$matchlogid,'teamid'=>$teamid,'mid'=>$this->mid))->first() ){
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

		$matchlogid = $request->get('matchlogid','');	
		$teamid = $request->get('teamid','');

		if(gettype($mtime)!='array'){
            return response()->json(array('error'=>1,'msg'=>'参数错误'));
            exit();
        }

        if(empty($address)){
            return response()->json(array('error'=>1,'msg'=>'请填写地址'));
            exit();
        }

		$dataArr = array('mtime'=>json_encode($mtime),'rname'=>$rname,'phone'=>$phone,'address'=>$address,'matchlogid'=>$matchlogid,'teamid'=>$teamid,'mid'=>$this->mid);
		$r = false;

		$rlt = Matchlogsetting::where(array('matchlogid'=>$matchlogid,'teamid'=>$teamid,'mid'=>$this->mid))->first();
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
		$matchlogid = $request->get('matchlogid','');	
		$teamid = $request->get('teamid','');
		$type = $request->get('type','');
		$val = $request->get('val','');	
		$valt = $request->get('valt','');	

		if(empty($matchlogid)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['matchlogid']= $matchlogid;	

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
        	if(!$teamArr = Teammember::where('mid','=',$this->mid)->where('isleader','=','y')->first()){
        		return response()->json(array('error'=>1,'msg'=>'无权操作'));
	            exit();
        	}	
        }

        if($type != 'b'){
			if(Matchlogcontent::where(array('matchlogid'=>$matchlogid,'teamid'=>$teamid,'mid'=>$this->mid,'type'=>$type))->first()){
				return response()->json(array('error'=>1,'msg'=>'已经填写'));
    			exit();	
			}				
		}

        $dataArr['mid']= $this->mid;
        $res = false;
        DB::beginTransaction();
            Matchlogcontent::create($dataArr);
            if(in_array($type,array('a'))){				
				Matchlog::where('id','=',$matchlogid)->update(array('status'=>'eupc'));
			}

			if(in_array($type,array('d','e'))){				
				Matchlog::where('id','=',$matchlogid)->update(array('status'=>'end'));
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
		$matchlogid = $request->get('matchlogid','');	
		$teamid = $request->get('teamid','');

		$mlogArr = Matchlog::select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city','created_at'])->where('id','=',$matchlogid)->first();
		if(empty($matchlogid) || empty($mlogArr)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['matchlogid']= $matchlogid;	

        if(empty($teamid)){
            return response()->json(array('error'=>1,'msg'=>'队伍id不能为空'));
            exit();
        }
        $dataArr['teamid']= $teamid;

        if(!$teamArr = Teammember::where('mid','=',$this->mid)->where('isleader','=','y')->first()){
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
        		Matchlog::where('id','=',$matchlogid)->update(['status'=>'mready']);
        		$res = true;  
        	DB::commit();
        }elseif($mlogArr->status=='mready'){ //添加开始比赛
        	$mlogcontentArr = Matchlogcontent::where(array('matchlogid'=>$matchlogid,'type'=>'f'))->first();
        	if($mlogcontentArr->mid == $this->mid || $mlogcontentArr->teamid == $teamid ){ //自己点的
        		return response()->json(array('error'=>1,'msg'=>'已经请求，请勿重复点击'));
            	exit();
        	}else{ //对方点的
        		DB::beginTransaction();
	        		Matchlogcontent::where(array('id'=>$mlogcontentArr->id))->update(['txt1'=>'s']);
	        		Matchlog::where('id','=',$matchlogid)->update(['status'=>'mgo']);
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

	//球员列表
	public function teammember(Request $request){
		$teamid = $request->get('teamid','');
        $listArr = Team::where('id','=',$teamid)->first(); 
        $listmemberYArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->where('mid','!=',$this->mid)->first(); 
        $listmemberArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->where('mid','!=',$this->mid)->get(); 
        $dataArr  = array();
        if(!empty($listArr)){
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr[$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr[$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr[$i]['number'] = $listmemberYArr->number;
                    $dataArr[$i]['name'] = $listmemberYArr->name;
                    $dataArr[$i]['isleader'] = $listmemberYArr->isleader;  
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr[$i]['mid'] = $v->member->id;
                    $dataArr[$i]['icon'] = $v->member->icon;
                    $dataArr[$i]['number'] = $v->number;
                    $dataArr[$i]['name'] = $v->name;
                    $dataArr[$i]['isleader'] = $v->isleader;  
                    $i++;
                }
            }
        }
		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();	
	}

	
	public function votesave(Request $request){	
		$matchlogid = $request->get('matchlogid','');	
		$teamid = $request->get('teamid','');
		$bestmid = $request->get('bestmid','');

		if(empty($matchlogid)){
            return response()->json(array('error'=>1,'msg'=>'比赛id不能为空'));
            exit();
        }
        $dataArr['matchlogid']= $matchlogid;	

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

        if(!$teamArr = Teammember::where('mid','=',$bestmid)->where('teamid','=',$teamid)->first()){
    		return response()->json(array('error'=>1,'msg'=>'只能推荐自己队伍的小伙伴'));
            exit();
    	}	

    	if(Vote::where(array('matchlogid'=>$matchlogid,'mid'=>$this->mid))->first()){
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