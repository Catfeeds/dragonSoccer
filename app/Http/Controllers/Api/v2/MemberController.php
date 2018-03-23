<?php
namespace App\Http\Controllers\Api\v2;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;

use App\Models\Members;
use App\Models\Gamesages;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Gamecollect;
use App\Models\Groupinvite;
use App\Models\Balancelog;
use App\Models\Orders;
use App\Models\Gteaminvite;
use App\Models\Relation;
use App\Models\Gteam;
use App\Models\Gteammembers;
use Hash;
use Config;

use Log;
class MemberController extends Controller
{
	private $mid = '';
	private $iArr = ['1'=>'待接受','2'=>'同意','3'=>'失效']; 
	private $fArr = ['1'=>'等待同意','2'=>'拒绝','3'=>'失效','4'=>'同意']; 
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
		$this->memberArr = Config::get('custom.member');
	}


	//用户查找
	public function searchlist(Request $request){
		$keywd = $request->get('keywd','');
		$groupid = $request->get('groupid','');

		$listArr  = array();
		$dataArr  = array();
		$groupArr = Group::find($groupid);
		if(!empty($groupArr)){
			$gaArr = Gamesages::find($groupArr->gamesagesid);
			if(!empty($gaArr)){
				if(!empty($keywd)){
					$listArr = Members::where(function ($query) use ($keywd) {
						$query->orWhere('truename','like','%'.$keywd.'%')->orWhere('mobile','like','%'.$keywd.'%');
					})->where('birthday','>=',date('Y-m-d',$gaArr->starttime))->where('birthday','<=',date('Y-m-d',$gaArr->endtime))->orderBy('id', 'desc')->limit(30)->get();

					/*$listArr = Members::where(function ($query) use ($keywd) {
						$query->orWhere('name','like','%'.$keywd.'%')->orWhere('mobile','like','%'.$keywd.'%');
					})->orderBy('id', 'asc')->limit(30)->get(['id','icon','name','mobile','status']);*/
				}
			}
		}

		if(!empty($listArr)){
			foreach ($listArr as $k => $v) {
				$dataArr2['status'] = 'y';
				$dataArr2['statusmsg'] = '发送邀请';
				if($v->id == $this->mid){
					continue;
				}

				$mid = $v->id;
				$gArr = Group::where(array('gamesagesid'=>$groupArr->gamesagesid))->whereHas('gmember',function($query) use ($mid){$query->select('mid','groupid')->where(array('mid'=>$mid));})->first();
				if(!empty($gArr)){
					$dataArr2['status'] = 'n';
					$dataArr2['statusmsg'] = '已有队伍';
				}

				if($v->status=='n'){
					$dataArr2['status'] = 'n';
					$dataArr2['statusmsg'] = '未认证';
				}

				if(Groupinvite::where(['groupid'=>$groupid,'fmid'=>$v->id,'status'=>'1'])->first()){
					$dataArr2['status'] = 'n';
					$dataArr2['statusmsg'] = '待接受';
				}

				$dataArr2['mid'] = $v->id;
				$dataArr2['icon'] = $v->icon;
				$dataArr2['name'] = empty($v->truename)?FunctionHelper::makemobilestar($v->mobile):$v->truename;
				$dataArr2['mobile'] = FunctionHelper::makemobilestar($v->mobile);
				$dataArr[] = $dataArr2;
			}
		}

		
		return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
		exit();
	}

    //搜索
    public function searchlistteam(Request $request){
        $keywd = $request->get('keywd','');
        $teamid = $request->get('teamid','');

        $listArr  = array();
        $dataArr  = array();
        $gteamArr = Gteam::find($teamid);
        if(!empty($gteamArr)){
            $gaArr = Gamesages::find($gteamArr->gamesagesid);
            if(!empty($gaArr)){
                if(!empty($keywd)){
                    $listArr = Members::where(function ($query) use ($keywd) {
                        $query->orWhere('truename','like','%'.$keywd.'%')->orWhere('mobile','like','%'.$keywd.'%');
                    })->where('birthday','>=',date('Y-m-d',$gaArr->starttime))->where('birthday','<=',date('Y-m-d',$gaArr->endtime))->orderBy('id', 'desc')->get();
                }
            }
        }

        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $dataArr2['status'] = 'y';
                $dataArr2['statusmsg'] = '发送邀请';
                if($v->id == $this->mid){
                    continue;
                }

                $mid = $v->id;
                $gArr = Gteam::where(array('gamesagesid'=>$gteamArr->gamesagesid))->whereHas('teammember',function($query) use ($mid){$query->select('mid','teamid')->where(array('mid'=>$mid));})->first();
                if(!empty($gArr)){
                    $dataArr2['status'] = 'n';
                    $dataArr2['statusmsg'] = '已有队伍';
                }

                if($v->status=='n'){
                    $dataArr2['status'] = 'n';
                    $dataArr2['statusmsg'] = '未认证';
                }

                if(Gteaminvite::where(['gteamid'=>$teamid,'fmid'=>$v->id,'status'=>'1'])->first()){
                    $dataArr2['status'] = 'n';
                    $dataArr2['statusmsg'] = '待接受';
                }

                $dataArr2['mid'] = $v->id;
                $dataArr2['icon'] = $v->icon;
                $dataArr2['name'] = empty($v->truename)?FunctionHelper::makemobilestar($v->mobile):$v->truename;
                $dataArr2['mobile'] = FunctionHelper::makemobilestar($v->mobile);
                $dataArr[] = $dataArr2;
            }
        }

        
        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }

	//12-05  个人数据
    public function infodata(){
        $listArr = Members::where('id','=',$this->mid)->first();
        $dataArr = array();
        if(!empty($listArr)){
            $dataArr['mid']         = $this->mid; 
            $dataArr['mobile']      = substr($listArr->mobile,0,3).'****'.substr($listArr->mobile,-4); 
            $dataArr['icon']        = empty($listArr->icon)?'':$listArr->icon; 
            $dataArr['name']        = empty($listArr->name)?'':$listArr->name;
            $dataArr['status']      = $this->memberArr['statusArr'][$listArr->status]; 

            $i = 7;
            if(empty($listArr->province)||empty($listArr->city)|| empty($listArr->country) || empty($listArr->address)){
                $i -=1; 
            }
            if(empty($listArr->school)){
                $i -=1; 
            }
            if(empty($listArr->position)){
                $i -=1; 
            }
            if(empty($listArr->foot)){
                $i -=1; 
            }
            if(empty($listArr->weight)){
                $i -=1; 
            }
            if(empty($listArr->height)){
                $i -=1; 
            }
            if(empty($listArr->img)){
                $i -=1; 
            }
            $dataArr['footballsts'] = $i==0?0:ceil($i*100/7);

            $dataArr['balance'] = empty($listArr->balance)?0:$listArr->balance;

            $dataArr['order'] = Orders::where('mid','=',$this->mid)->count();

            $dataArr['apply'] = Gteammembers::where('mid','=',$this->mid)->whereHas('team',function($q){ $q->where('type','m');})->count();

            $dataArr['matchcollect'] = Gamecollect::where('mid','=',$this->mid)->count();

            $bArr = Balancelog::where('mid','=',$this->mid)->where('created_at','>=',date('Y-m-d'))->where('created_at','<=',date('Y-m-d').' 23:59:59')->first();
            $dataArr['issign'] = empty($bArr)?'n':'y';
           
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    //消息提醒
    public function allmsg(Request $request){
        $dataArr  = array('member'=>array(),'group'=>array(),'gteam'=>array());

        //好友邀请
        $relationArr = Relation::with('member')->where('friend_mid','=',$this->mid)->orderBy('id','desc')->get();       
        if(!empty($relationArr)){
            $dataArr1  = array();
            foreach ($relationArr as $k => $v) {
                $dataArr1[$k]['mid'] = $v->member->id;
                $dataArr1[$k]['icon'] = $v->member->icon;
                $dataArr1[$k]['name'] = $v->member->name;
                $dataArr1[$k]['msg'] = '系统提示';
                $dataArr1[$k]['status'] = $v->status;
                $dataArr1[$k]['statusmsg'] = $this->fArr[$v->status];
                $dataArr1[$k]['time'] = date('Y年m月d日 H:i:s',strtotime($v->created_at));
                $dataArr1[$k]['title'] = '添加好友';
            }
            $dataArr['member'] = $dataArr1;
        }

        //比赛邀请
        $groupArr = Groupinvite::wherehas('group')->with('group.gamesages','group.gamesages.games','members')->where('fmid','=',$this->mid)->orderBy('id','desc')->get();
        if(!empty($groupArr)){
            $dataArr2  = array();
            foreach ($groupArr as $k => $v) {
                $dataArr2[$k]['groupid'] = $v->groupid;
                $dataArr2[$k]['icon'] = empty($v->members)?'':$v->members->icon;
                $dataArr2[$k]['name'] = empty($v->members)?'':$v->members->name;
                $dataArr2[$k]['msg'] = empty($v->group->gamesages->games)?'':$v->group->gamesages->games->name.'('.$v->group->gamesages->val.')';
                $dataArr2[$k]['status'] = $v->status;
                $dataArr2[$k]['statusmsg'] = $this->iArr[$v->status];
                $dataArr2[$k]['time'] = date('Y年m月d日 H:i:s',strtotime($v->created_at));
                $dataArr2[$k]['title'] = '组队邀请';
            }
            $dataArr['group'] = $dataArr2;
        }

        //比赛邀请
        $gteamArr = Gteaminvite::with('gteam.gamesages','gteam.gamesages.games','members')->where('fmid','=',$this->mid)->orderBy('id','desc')->get();
        if(!empty($gteamArr)){
            $dataArr3  = array();
            foreach ($gteamArr as $k => $v) {
                $dataArr3[$k]['gteamid'] = $v->id;
                $dataArr3[$k]['icon'] = empty($v->gteam->icon)?'':$v->gteam->icon;
                $dataArr3[$k]['name'] = empty($v->gteam)?'':$v->gteam->name;
                $dataArr3[$k]['msg'] = empty($v->gteam->gamesages->games)?'':$v->gteam->gamesages->games->name.'('.$v->gteam->gamesages->val.')';
                $dataArr3[$k]['status'] = $v->status;
                $dataArr3[$k]['statusmsg'] = $this->iArr[$v->status];
                $dataArr3[$k]['time'] = date('Y年m月d日 H:i:s',strtotime($v->created_at));
                $dataArr3[$k]['title'] = '参赛邀请';
            }
            $dataArr['gteam'] = $dataArr3;
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
		exit();
    }

    public function kf(){
        if($kfArr = Redis::lrange('kfWaitList',0,-1)){
            $l = rand(0, count($kfArr)-1 );
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>['kf'=>$kfArr[$l]]));
            exit();
        }
        return response()->json(array('error'=>0,'msg'=>'客服不在线','data'=>['kf'=>'']));
        exit();
    }

    public function getcollectlist(){
        $listArr = Gamecollect::with('game')->where('mid','=',$this->mid)->get();
        $dataArr  = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                if(empty($v->game)){
                    continue;
                }
                $dataArr[$k]['matchid'] = $v->game->id;
                $dataArr[$k]['title'] = $v->game->name;
                $dataArr[$k]['creattime'] = substr($v->created_at,0,10);
            }
        }

        return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
        exit();
    }
    
}