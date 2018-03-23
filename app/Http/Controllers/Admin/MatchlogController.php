<?php
namespace App\Http\Controllers\Admin;

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
	public function index(Request $request){
		$query = new Matchlog();
		
        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query =  $query->with(array('match'=>function($query){
                $query->select('id','name');
            }));
        $listArr = $query->paginate(10);        
        return view('admin.matchlog_index')->with('listArr',$listArr)->with('matchlogArr',$this->matchlogArr)->with('matchArr',$this->matchArr);
	}

	//赛事安排   
    public function view(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        $setArr = array();
        $contentArr = array();
        if(!empty($id)){
            $query = Matchlog::where('id','=',$id);
		
	        $query = $query->with(array('ateam'=>function ($query){
	                $query->select('id','name','icon');
	            }));
	        $query = $query->with(array('bteam'=>function ($query){
	                $query->select('id','name','icon');
	            }));
	        $query =  $query->with(array('match'=>function($query){
	                $query->select('id','name');
	            }));
	        $listArr = $query->first(); 

	        //地址选择
	        $setArr = Matchlogsetting::with('team','member')->where('matchlogid','=',$id)->orderBy('teamid', 'asc')->get();

	        //比赛结果  
	        $contentArr = Matchlogcontent::with('team','member')->where('matchlogid','=',$id)->where('type','=','a')->orderBy('teamid', 'asc')->get();     
        }
        return view('admin.matchlog_view')->with('listArr',$listArr)->with('setArr',$setArr)->with('contentArr',$contentArr)->with('matchlogArr',$this->matchlogArr)->with('matchArr',$this->matchArr);
    }

    //保存数据   
    public function ajaxsave(Request $request){
    	$res = false;

        $id = $request->get('id','');
        if(empty($id)){
            return response()->json(array('error'=>1,'msg'=>'请填写id'));
        }
        $listArr = $query = Matchlog::where('id','=',$id)->first();

        $status = $request->get('status','1');

        $type = $request->get('type','1');
        if($type==1){
        	$stime = $request->get('stime','');
        	$stimet = $request->get('stimet','');
        	$address = $request->get('address','');

        	if(empty($stime) ||empty($stimet) ||empty($address) ){
	            return response()->json(array('error'=>1,'msg'=>'参数不能为空'));
	        }	        

	        $res = Matchlog::where('id','=',$id)->update(array('status'=>$status,'stime'=>strtotime($stime.' '.$stimet.':00:00'),'address'=>$address));  
        }

        if($type==2){
        	$successteamid = $request->get('successteamid','');
        	$successscore = $request->get('successscore','');
        	$failedscore = $request->get('failedscore','');

        	if(empty($successteamid)){
	            return response()->json(array('error'=>1,'msg'=>'参数不能为空'));
	        }

	        if($successteamid=='all'){
	        	DB::beginTransaction();
	        		Team::where(array('id'=>$listArr->ateamid))->update(array('sts'=>'f'));
                    Team::where(array('id'=>$listArr->bteamid))->update(array('sts'=>'f'));
		            Matchlog::where('id','=',$id)->update(array('status'=>$status));
		            $res = true; 
		        DB::commit();
	        }else{
	        	DB::beginTransaction();
		        	$arr = array();
		        	if($listArr->ateamid == $successteamid){
		        		$arr['ateamscore'] = $successscore;
		        		$arr['bteamscore'] = $failedscore;
		        		$arr['successteamid'] = $listArr->ateamid;
		        		$arr['failedteamid'] = $listArr->bteamid;

		        		Team::where(array('id'=>$listArr->ateamid))->update(array('sts'=>'s'));
                    	Team::where(array('id'=>$listArr->bteamid))->update(array('sts'=>'f'));
		        	}else{
		        		$arr['bteamscore'] = $successscore;
		        		$arr['ateamscore'] = $failedscore;
		        		$arr['successteamid'] = $listArr->bteamid;
		        		$arr['failedteamid'] = $listArr->ateamid;	
		        		Team::where(array('id'=>$listArr->ateamid))->update(array('sts'=>'f'));
                    	Team::where(array('id'=>$listArr->bteamid))->update(array('sts'=>'s'));
		        	}
		        	$arr['status'] = $status;
		            Matchlog::where('id','=',$id)->update($arr);
		            $res = true; 
		        DB::commit();
	        }
        }

        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.matchlog.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }
}