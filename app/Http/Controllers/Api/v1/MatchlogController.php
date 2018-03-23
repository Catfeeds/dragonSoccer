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
use App\Models\Vote;

use Hash;
use DB;
use Config;
class MatchlogController extends Controller
{
	private $teamArr = '';
	private $memberArr = '';
	private $systemArr = '';
	private $matchArr = '';
	private $matchlogArr = '';
	public function __construct(Request $request){
		$this->teamArr = Config::get('custom.team');
		$this->memberArr = Config::get('custom.member');
		$this->systemArr = Config::get('custom.system');
		$this->matchArr = Config::get('custom.match');
		$this->matchlogArr = Config::get('custom.matchlog');
	}

	//赛事安排
	public function getall(Request $request){
		$matchid = $request->get('matchid','');
		$province = $request->get('province','');
		$date = $request->get('date','');

		$query = Matchlog::where('matchid','=',$matchid);
		if(!empty($province) && $province!='全部赛区'){
			$query = $query->where('province','=',$province);
		}

		if(!empty($date)){
			$time = strtotime($date);
			$query = $query->where('stime','>=',$time)->where('stime','<=',$time+3600*24);
		}

        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name');
            }));
        $dataArr = $query->select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->get();

        $dataArr2 = array();
        foreach ($dataArr as $k => $v) {
        	$key = '';
        	if(substr($v->matchlevel,0,1)=='c'){
        		$key = $v->city; 
        	}

        	if(substr($v->matchlevel,0,1)=='p'){
        		$key = $v->province; 	 
        	}

        	if(substr($v->matchlevel,0,1)=='t'){
        		$key = '全国'; 	 
        	}

        	$arr = array();
        	$arr['matchlogid'] = $v->id;
        	$arr['matchname'] = $v->match->name;
        	$arr['ateamid'] = $v->ateamid;
        	$arr['ateamname'] = $v->ateam->name;
        	$arr['ateamicon'] = $v->ateam->icon;
        	$arr['ateamscore'] = $v->ateamscore;
        	$arr['bteamid'] = $v->bteamid;
        	$arr['bteamname'] = empty($v->bteamid)?'':$v->bteam->name;
        	$arr['bteamicon'] = empty($v->bteamid)?'':$v->bteam->icon;
        	$arr['bteamscore'] = $v->bteamscore;

        	$status = '即将开始';
        	if($v->status=='e'){
        		$status = $v->ateamscore.':'.$v->bteamscore;
        	}else{
        		$status = $this->matchlogArr['statusArr'][$v->status];
        	}
        	$arr['status'] = $status;

        	$dataArr2[$key]['lists'][] = $arr;
        	$dataArr2[$key]['title'] = $key;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array_values($dataArr2) ));
        exit();
	}


	//赛事安排
	public function getallbyday(Request $request){
		$matchid = $request->get('matchid','');
		$query = Matchlog::where('matchid','=',$matchid);
		$time = strtotime(date('Y-m-d'));
		$query = $query->where('stime','>=',$time)->where('stime','<=',$time+3600*24);
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name');
            }));
        $dataArr = $query->select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->get();

        $dataArr2 = array();
        foreach ($dataArr as $k => $v) {
        	$key = '';
        	if(substr($v->matchlevel,0,1)=='c'){
        		$key = $v->city; 
        	}

        	if(substr($v->matchlevel,0,1)=='p'){
        		$key = $v->province; 	 
        	}

        	if(substr($v->matchlevel,0,1)=='t'){
        		$key = '全国'; 	 
        	}

        	$arr = array();
        	$arr['matchlogid'] = $v->id;
        	$arr['matchname'] = $v->match->name;
        	$arr['ateamid'] = $v->ateamid;
        	$arr['ateamname'] = $v->ateam->name;
        	$arr['ateamicon'] = $v->ateam->icon;
        	$arr['ateamscore'] = $v->ateamscore;
        	$arr['bteamid'] = $v->bteamid;
        	$arr['bteamname'] = empty($v->bteamid)?'':$v->bteam->name;
        	$arr['bteamicon'] = empty($v->bteamid)?'':$v->bteam->icon;
        	$arr['bteamscore'] = $v->bteamscore;

        	$status = '即将开始';
        	if($v->status=='e'){
        		$status = $v->ateamscore.':'.$v->bteamscore;
        	}else{
        		$status = $this->matchlogArr['statusArr'][$v->status];
        	}
        	$arr['status'] = $status;

        	$dataArr2[$key]['lists'][] = $arr;
        	$dataArr2[$key]['title'] = $key;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>array_values($dataArr2) ));
        exit();
	}


	//赛事安排
	public function getone(Request $request){
		$id = $request->get('matchlogid','');
		$mlogArr = Matchlog::with('ateam','bteam')->where('id','=',$id)->first();
		$dataArr = array();
		$dataArr['matchlogid']= $mlogArr->id;
    	$dataArr['matchname'] = $mlogArr->match->name;

    	$dataArr['stime'] = empty($mlogArr->stime)?'':date('m月d日 H:i');
    	$dataArr['address'] = $mlogArr->address;

    	$dataArr['ateamname'] = $mlogArr->ateam->name;
    	$dataArr['ateamicon'] = $mlogArr->ateam->icon;
    	$dataArr['bteamname'] = empty($mlogArr->bteamid)?'':$mlogArr->bteam->name;
    	$dataArr['bteamicon'] = empty($mlogArr->bteamid)?'':$mlogArr->bteam->icon;
    	$status = '即将开始';
    	if($mlogArr->status=='e'){
    		$status = $mlogArr->ateamscore.':'.$mlogArr->bteamscore;
    	}else{
    		$status = $this->matchlogArr['statusArr'][$mlogArr->status];
    	}
    	$dataArr['status'] = $status;

    	$dataArr['ateam'] = array();
    	if(!empty($mlogArr->ateamid)){
    		$atArr = Teammember::where('teamid','=',$mlogArr->ateamid)->get();
    		if(!empty($atArr)){
    			foreach ($atArr as $k => $v) {
    				$tmp = array();
    				$tmp['name'] = $v->name; 
    				$tmp['icon'] = $v->icon;
    				$dataArr['ateam'][] = $tmp;
    			}
    		}
    	}

    	$dataArr['bteam'] = array();
    	if(!empty($mlogArr->bteamid)){
    		$atArr = Teammember::where('teamid','=',$mlogArr->bteamid)->get();
    		if(!empty($atArr)){
    			foreach ($atArr as $k => $v) {
    				$tmp = array();
    				$tmp['name'] = $v->name; 
    				$tmp['icon'] = $v->icon;
    				$dataArr['bteam'][] = $tmp; 
    			}
    		}
    	}
		

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr ));
        exit();
	}

	//赛事安排日期
	public function getallbydate(Request $request){
		$matchid = $request->get('matchid','');
		$month = $request->get('month',date('m'));
		$year = $request->get('year',date('Y'));

		$time = strtotime("+".($month-1)." month",strtotime($year."-01-01"));
		$days = date('t',$time);
		//echo time();

		$dataArr = array();
		for($i=1;$i<=$days;$i++){
			$stime = $time+3600*24*($i-1);
			$etime = $time+3600*24*$i;
			$r = Matchlog::where('matchid','=',$matchid)->where('stime','>=',$stime)->where('stime','<=',$etime)->first();
			$dataArr[] =	empty($r)?false:true;
		}

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//赛事安排赛区
	public function getallbyprovince(Request $request){
		$matchid = $request->get('matchid','');

		$r = Matchlog::where('matchid','=',$matchid)->groupBy('province')->get(['province']);
		$dataArr = array();
		if(!empty($r)){
			foreach ($r as $v) {
				$dataArr[] = $v->province;
			}
		}
		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//比赛结果--省市
	public function matchrankcity(Request $request){
		$matchid = $request->get('matchid','');
		$province = $request->get('province','');
		$dataArr = array();

		if(!empty($province) && $province!='全部赛区'){
			$pArr = Team::where('matchid','=',$matchid)->where('province','=',$province)->get(['province']);
		}else{
			$pArr = Team::where('matchid','=',$matchid)->groupBy('province')->get(['province']);
		}

		//var_dump($pArr);
		if(!empty($pArr)){
			foreach ($pArr as $kp => $vp) {
				$pdataArr = array();
				$pdataArr['pname'] = $vp->province;

				$matchlevelArr = array('c1','c2','c3');	
				$cArr = Team::where('matchid','=',$matchid)->where('province','=',$vp->province)->whereIn('level',$matchlevelArr)->groupBy('city')->get(['city']);
				//var_dump($cArr);
				if(!empty($cArr)){
					foreach ($cArr as $kc => $vc){
						$cdataArr = array();
						$cdataArr['cname'] = $vc->city;

						$teamidArr = Team::where('matchid','=',$matchid)->where('province','=',$vp->province)->where('city','=',$vc->city)->get(['id','name','sts']);
						if(!empty($teamidArr)){													
							foreach ($teamidArr as $kid => $tid) {  //按比分排序 
								$tmpArr = array();
								$tmpArr['teamid'] = $tid->id;
								$tmpArr['name'] = $tid->name;
								$tmpArr['success'] = Matchlog::where('successteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();
								$tmpArr['failed'] = Matchlog::where('failedteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();

								
								$tmpArr['sts'] = $this->teamArr['stsArr'][$tid->sts];

								$cdataArr['cdata'][] = $tmpArr;
							}
							if(!empty($cdataArr['cdata'])){
								//处理数据
								$cdataArr['cdata'] = FunctionHelper::arrayRsort($cdataArr['cdata'],'success');
								foreach ($cdataArr['cdata'] as $k => &$v) {
									$v['ranknumber'] = $k+1;	
								}
							}
						}
						$pdataArr['pdata'][] = $cdataArr;	
					}
				}
				$dataArr[] = $pdataArr;
			}

		}

		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//比赛结果--省
	public function matchrankprovince(Request $request){
		$matchid = $request->get('matchid','');
		$province = $request->get('province','');
		$dataArr = array();
		
		if(!empty($province) && $province!='全部赛区'){
			$pArr = Team::where('matchid','=',$matchid)->where('province','=',$province)->get(['province']);
		}else{
			$pArr = Team::where('matchid','=',$matchid)->groupBy('province')->get(['province']);
		}

		//var_dump($pArr);
		if(!empty($pArr)){
			foreach ($pArr as $kp => $vp) {
				$pdataArr = array();
				$pdataArr['pname'] = $vp->province;

				$matchlevelArr = array('p1','p2','p3','p4');
				$teamidArr = Team::where('matchid','=',$matchid)->where('province','=',$vp->province)->whereIn('level',$matchlevelArr)->get(['id','name','sts']);
				if(!empty($teamidArr)){												
					foreach ($teamidArr as $kid=>$tid) {  //按比分排序 
						$tmpArr = array();
						$tmpArr['teamid'] = $tid->id;
						$tmpArr['name'] = $tid->name;
						$tmpArr['success'] = Matchlog::where('successteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();
						$tmpArr['failed'] = Matchlog::where('failedteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();

						$tmpArr['sts'] = $this->teamArr['stsArr'][$tid->sts];

						$pdataArr['pdata'][] = $tmpArr;
					}

					if(!empty($pdataArr['pdata'])){
						//处理数据
						$pdataArr['pdata'] = FunctionHelper::arrayRsort($pdataArr['pdata'],'success');
						foreach ($pdataArr['pdata'] as $k => &$v) {
							$v['ranknumber'] = $k+1;	
						}
					}
				}

				$dataArr[] = $pdataArr;
			}

		}

		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//比赛结果--全国
	public function matchrankcountry(Request $request){
		$matchid = $request->get('matchid','');
		$dataArr = array();

		$matchlevelArr = array('t1');
		$teamidArr = Team::where('matchid','=',$matchid)->whereIn('level',$matchlevelArr)->get(['id','name','sts']);
		if(!empty($teamidArr)){												
			foreach ($teamidArr as $kid=>$tid) {  // 按比分排序
				$tmpArr = array();
				$tmpArr['teamid'] = $tid->id;
				$tmpArr['name'] = $tid->name;
				$tmpArr['success'] = Matchlog::where('successteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();
				$tmpArr['failed'] = Matchlog::where('failedteamid','=',$tid->id)->whereIn('matchlevel',$matchlevelArr)->count();

				$tmpArr['ranknumber'] = $kid+1;
				$tmpArr['sts'] = $this->teamArr['stsArr'][$tid->sts];

				$dataArr[] = $tmpArr;
			}

			//处理数据
			if(!empty($dataArr)){
				$dataArr = FunctionHelper::arrayRsort($dataArr,'success');
				foreach ($dataArr as $k => &$v) {
					$v['ranknumber'] = $k+1;	
				}
			}
		}

		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//比赛结果详情
	public function matchrankinfo(Request $request){
		$matchid = $request->get('matchid','');
		$type = $request->get('type','c');
		$val = $request->get('val','');
		$dataArr = array();

		$query = Matchlog::where('matchid','=',$matchid);
		if($type=='c'){
			$query = $query->where('city','=',$val)->whereIn('matchlevel',array('c1','c2','c3'));
		}
		if($type=='p'){
			$query = $query->where('province','=',$val)->whereIn('matchlevel',array('p1','p2','p3','p4'));
		}

		if($type=='t'){
			$query = $query->whereIn('matchlevel',array('t1'));
		}
		
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name');
            }));
        $dataArr = $query->select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->get();


		$dataArr2 = array();
        foreach ($dataArr as $k => $v) {
        	$arr = array();
        	$arr['stime'] = empty($v->stime)?'--':date('m月d日',$v->stime);
        	$arr['ateamname'] = $v->ateam->name;
        	$arr['bteamname'] = empty($v->bteamid)?'':$v->bteam->name;

        	$status = '即将开始';
        	if($v->status=='e'){
        		$status = $v->ateamscore.':'.$v->bteamscore;
        	}else{
        		$status = $this->matchlogArr['statusArr'][$v->status];
        	}
        	$arr['status'] = $status;

        	$dataArr2[] = $arr;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr2 ));
        exit();
	}

	
	//队伍详情
	public function matchrankteam(Request $request){
		$matchid = $request->get('matchid','');
		$dataArr = array();

		$teamidArr = Team::where('matchid','=',$matchid)->get(['id','icon','name','sts']);
		if(!empty($teamidArr)){												
			foreach ($teamidArr as $kid=>$tid) {  //按比分排序 
				$tmpArr = array();
				$tmpArr['icon'] = $tid->icon;
				$tmpArr['name'] = $tid->name;
				$tmpArr['success'] = Matchlog::where('successteamid','=',$tid->id)->count();
				$tmpArr['failed'] = Matchlog::where('failedteamid','=',$tid->id)->count();
				$tmpArr['ranknumber'] = $kid+1;
				$dataArr[] = $tmpArr;
			}
			//处理数据
			if(!empty($dataArr)){
				$dataArr = FunctionHelper::arrayRsort($dataArr,'success');
				foreach ($dataArr as $k => &$v) {
					$v['ranknumber'] = $k+1;	
				}
			}
		}

		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}

	//队伍比赛结果详情
	public function matchrankteaminfo(Request $request){
		$matchid = $request->get('matchid','');
		$teamid = $request->get('teamid','');
		$dataArr = array('name'=>'','membernumber'=>0,'member'=>array(),'lists'=>array());

		$listArr = Team::where('matchid','=',$matchid)->where('id','=',$teamid)->first(); 
        $listmemberYArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','y')->first(); 
        $listmemberArr = Teammember::with('member')->where('teamid','=',$teamid)->where('isleader','=','n')->limit(2)->get(); 
        $num = Teammember::where('teamid','=',$teamid)->count(); 
        if(!empty($listArr)){
            $dataArr['name'] = $listArr->name;
            $dataArr['membernumber'] = $num;
            if(!empty($listmemberArr)){
                $i = 0;
                if(!empty($listmemberYArr)){
                    $dataArr['member'][$i]['mid'] = $listmemberYArr->member->id;
                    $dataArr['member'][$i]['icon'] = $listmemberYArr->member->icon;
                    $dataArr['member'][$i]['name'] = $listmemberYArr->name;
                    $dataArr['member'][$i]['isleader'] = 'y';   
                    $dataArr['member'][$i]['number'] = $listmemberYArr->number;   
                    $i++;
                }
                foreach ($listmemberArr as $k => $v) {
                    $dataArr['member'][$i]['mid'] = $v->member->id;
                    $dataArr['member'][$i]['icon'] = $v->member->icon;
                    $dataArr['member'][$i]['name'] = $v->name;
                    $dataArr['member'][$i]['isleader'] = 'n';  
                    $dataArr['member'][$i]['number'] = $v->number;  
                    $i++;
                }
            }
        }


		//比赛详情
		$query = Matchlog::where('matchid','=',$matchid);
		$query = $query->where(function ($query) use ($teamid) {
            $query->orWhere('ateamid','=',$teamid)->orWhere('bteamid','=',$teamid);
        });
		
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name');
            }));
        $dataArr2 = $query->select(['id','matchid','ateamid', 'ateamscore','bteamid','bteamscore','matchlevel','status','stime','province','city'])->get();
        

        $tmpArr = array();
        foreach ($dataArr2 as $k => $v) {
        	$key = '';
        	$keymsg = '';
        	if(substr($v->matchlevel,0,1)=='c'){
        		$key = 'c'; 
        		$keymsg = '市';
        	}

        	if(substr($v->matchlevel,0,1)=='p'){
        		$key = 'p';
        		$keymsg = '省'; 	 
        	}

        	if(substr($v->matchlevel,0,1)=='t'){
        		$key = 't';
        		$keymsg = '全国'; 	 
        	}

        	$arr = array();
        	$arr['ateamname'] = $v->ateam->name;
        	$arr['ateamicon'] = $v->ateam->icon;
        	$arr['bteamname'] = empty($v->bteamid)?'':$v->bteam->name;
        	$arr['bteamicon'] = empty($v->bteamid)?'':$v->bteam->icon;

        	$status = '即将开始';
        	if($v->status=='e'){
        		$status = $v->ateamscore.':'.$v->bteamscore;
        	}else{
        		$status = $this->matchlogArr['statusArr'][$v->status];
        	}
        	$arr['status'] = $status;
        	$tmpArr[$key]['content'][] = $arr; 
        	$tmpArr[$key]['title'] = $keymsg; 
        }
		$dataArr['lists'] = array_values($tmpArr);

		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
	}
}