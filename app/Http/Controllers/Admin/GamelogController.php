<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Gteam;
use App\Models\Gteammembers;
use App\Models\Gamelog;

use App\Models\Gamesruler;
use App\Models\Gamesrulerinfo;

use Hash;
use DB;
use Config;
class GamelogController extends Controller
{
    private $statusArr = ['mw'=>'待定','mc'=>'待审核','mwate'=>'准备','mready'=>'即将开始','mgo'=>'开始','end'=>'比赛结束','eupc'=>'结果审核','eover'=>'结束'];
	public function __construct(Request $request){

	}

    public function index() {        
        $query = new Gamelog();
        $query = $query->with(array('gamesages'=>function ($query){
                $query->select('id','gamesid','starttime','endtime');
            }));             
        $query = $query->with(array('gamesages.games'=>function ($query){
                $query->select('id','name');
            }));

        $query = $query->with(array('ateam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $query = $query->with(array('bteam'=>function ($query){
                $query->select('id','name','icon');
            }));
        $listArr = $query->orderBy('id', 'desc')->paginate(20);

        return view('admin.gamelog_index')->with('listArr',$listArr)->with('statusArr',$this->statusArr);
    }

    //获取数据   
    public function add(Request $request){ 
        $gamesArr = Games::orderBy('sid', 'desc')->get();        
        return view('admin.gamelog_add')->with('gamesArr',$gamesArr);
    }

    public function ajaxages(Request $request){  
        $gid = $request->get('gid','');
        $ageArr = Gamesages::where('gamesid','=',$gid)->get();

        $str = '<option value="">请选择</option>';
        if(!empty($ageArr)){
            foreach ($ageArr as $v) {
                $str .= ('<option value="'.$v->id.'">'.date('Y/m/d',$v->starttime).'--'.date('Y/m/d',$v->endtime).'</option>');
            }
        }       
        return $str;
    }

    public function ajaxteam(Request $request){  
        $gaid = $request->get('gaid','');        
        $gArr = Gteam::with('teammember','teammember.member')->where('gamesagesid',$gaid)->whereIn('status',['w','s'])->orderBy('id', 'desc')->get();  
        return view('admin.gamelog_ajaxteam')->with('gArr',$gArr);
    }


    public function ajaxteamruler(Request $request){  
        $gid = $request->get('gid',''); 
        $grArr = Gamesrulerinfo::whereHas('ruler',function($q) use ($gid){ $q->where('gamesid',$gid);})->get();


        $str = '<option value="">请选择</option>';
        if(!empty($grArr)){
            foreach ($grArr as $v) {
                $str .= ('<option value="'.$v->key.'">'.$v->key.'</option>');
            }
        }       
        return $str;
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        $insert['groupsn'] = empty($input['groupsn'])?'':$input['groupsn'];
        $insert['ateamid'] = empty($input['ateamid'])?'':$input['ateamid'];
        $insert['bteamid'] = empty($input['bteamid'])?'':$input['bteamid'];       
        $insert['address'] = empty($input['address'])?'':$input['address'];       
        $insert['matchlevel'] = empty($input['matchlevel'])?'':$input['matchlevel'];       
        $insert['stime'] = empty($input['stime'])?'':strtotime($input['stime']);       
        $insert['gamesagesid'] = $input['gamesagesid'];

        $flag = false;
        if(empty($insert['ateamid']) && empty($insert['ateamid'])){
            $teamids = $request->get('teamids',[]);
            if(count($teamids)=='2'){
                $insert['ateamid'] = $teamids[0];
                $insert['bteamid'] = $teamids[1];
                $gArr = Gteam::where('id',$teamids[0])->first();
                $insert['province'] = $gArr->province;
                $insert['city'] = ($gArr->city=='市辖区'||$gArr->city=='县')?$gArr->country:$gArr->city; 
                $flag = true;
            }else{
                return response()->json(array('error'=>1,'msg'=>'每次只能选取两个队伍'));
                exit();   
            }
        }
        
        try {             
            $res = false;            
            DB::beginTransaction();
                $r1 = Gamelog::create($insert);                
                if($flag){                    
                    Gteam::whereIn('id',[$insert['ateamid'],$insert['bteamid']])->update(['status'=>'ww']);
                }
                
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.gamelog.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }           
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }


    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');  
        $listArr = Gamelog::with('gamesages','gamesages.games','ateam','bteam')->find($id);

        return view('admin.gamelog_edit')->with('listArr',$listArr);
    }


    public function ajaxdelmember(Request $request){
        $id = $request->get('id','');
        if(Gteammembers::destroy((int)$id)){           
            return response()->json(array('error'=>0,'msg'=>'删除成功','url'=>route('admin.group.index')));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
        exit();
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $id = $request->get('id','');
        $input = $request->all();
        $arr['ateamid'] = empty($input['ateamid'])?'':$input['ateamid'];
        $arr['bteamid'] = empty($input['bteamid'])?'':$input['bteamid'];   
        $arr['ateamscore'] = empty($input['ateamscore'])?'0':$input['ateamscore'];   
        $arr['bteamscore'] = empty($input['bteamscore'])?'0':$input['bteamscore'];   
        $arr['successteamid'] = empty($input['successteamid'])?'0':$input['successteamid'];   
        $insert['address'] = empty($input['address'])?'':$input['address'];       
        $insert['stime'] = empty($input['stime'])?'':strtotime($input['stime']); 
        
        $listArr = Gamelog::find($id);
        try {             
            $res = false;            
            DB::beginTransaction();
                if($listArr->ateamid == $arr['successteamid']){
                    $arr['failedteamid'] = $listArr->bteamid;

                    Gteam::where(array('id'=>$listArr->ateamid))->update(array('status'=>'s'));
                    Gteam::where(array('id'=>$listArr->bteamid))->update(array('status'=>'f'));
                }
                if($listArr->bteamid == $arr['successteamid']){
                    $arr['failedteamid'] = $listArr->ateamid;   
                    Gteam::where(array('id'=>$listArr->ateamid))->update(array('status'=>'f'));
                    Gteam::where(array('id'=>$listArr->bteamid))->update(array('status'=>'s'));
                }
                $arr['status'] = 'eover';//结束比赛
                Gamelog::where('id','=',$id)->update($arr);
                $res = true; 
            DB::commit();

            if($res){
                return response()->json(array('error'=>0,'msg'=>'成功','url'=>route('admin.gamelog.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }           
        return response()->json(array('error'=>1,'msg'=>'失败'));
    }
}