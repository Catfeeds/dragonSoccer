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

use Hash;
use DB;
use Config;
class GteamController extends Controller
{
    private $bucket = 'lzsn-icon';
    private $dir = 'gteam/';
    private $teamArr = ['w'=>'匹配成功','ww'=>'待定','s'=>'晋级','f'=>'淘汰'];
	public function __construct(Request $request){
	}

    public function index() {        
        $listArr = Gteam::where('type','=','m')->orderBy('id', 'desc')->paginate(20);
        return view('admin.gteam_index')->with('listArr',$listArr)->with('teamArr',$this->teamArr);
    }

    //获取数据   
    public function add(Request $request){ 
        $gamesArr = Games::orderBy('sid', 'desc')->get();        
        return view('admin.gteam_add')->with('gamesArr',$gamesArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
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

    public function ajaxgroup(Request $request){  
        $gaid = $request->get('gaid','');        
        $gArr = Group::with('gmember','gmember.members')->where('gamesagesid',$gaid)->where('status','1')->orderBy('id', 'desc')->get();  
        return view('admin.gteam_ajaxgroup')->with('gArr',$gArr);
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        $insert['icon'] = empty($input['icon'])?'':$input['icon'];
        $insert['name'] = empty($input['name'])?'':$input['name'];
        $insert['type'] = 'm';        
        $insert['gamesagesid'] = $input['gamesagesid'];
        
        $gids = $request->get('gids',[]);
        try {             
            $res = false;            
            DB::beginTransaction();
                $r1 = Gteam::create($insert);
                foreach ($gids as $k => $v) {
                    $gArr = Group::where(['id'=>$v])->first();
                    $gmArr =Groupmembers::where(['groupid'=>$v])->get();
                    foreach ($gmArr as $kk=> $vv) {
                        Gteammembers::create(['teamid'=>$r1->id,'mid'=>$vv->mid,'isleader'=>$k==0&&$kk==0?'y':'n' ]); 
                    }
                    Group::where('id','=',$v)->update(['status'=>4]); //完成组队 ？？？？ 
                }
                if(!empty($gArr)){
                    $update['province'] = $gArr->province;
                    $update['city'] = ($gArr->city=='市辖区'||$gArr->city=='县')?$gArr->country:$gArr->city; 
                    Gteam::where('id',$r1->id)->update($update);
                }
                
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.gteam.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }           
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }


    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');  
        $listArr = Gteam::with('gamesages','gamesages.games')->find($id);
        $gmArr = Gteammembers::with('member')->where('teamid',$id)->get();

        return view('admin.gteam_edit')->with('listArr',$listArr)->with('gmArr',$gmArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
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
        $insert['icon'] = empty($input['icon'])?'':$input['icon'];
        $insert['name'] = empty($input['name'])?'':$input['name'];
        
        $gids = $request->get('gids',[]);
        try {             
            $res = false;            
            DB::beginTransaction();
                $r1 = Gteam::where('id',$id)->update($insert);
                foreach ($gids as $k => $v) {
                    $gArr = Group::where(['id'=>$v])->first();
                    $gmArr = Groupmembers::where(['groupid'=>$v])->get();
                    foreach ($gmArr as $kk=> $vv) {
                        Gteammembers::create(['teamid'=>$id,'mid'=>$vv->mid]); 
                    }
                    Group::where('id','=',$v)->update(['status'=>4]);  
                }
                if(!empty($gArr)){
                    $update['province'] = $gArr->province;
                    $update['city'] = ($gArr->city=='市辖区'||$gArr->city=='县')?$gArr->country:$gArr->city; 
                    Gteam::where('id',$id)->update($update);
                }
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.gteam.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }           
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }

    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(Gteam::destroy((int)$id)){
                if(Gteammembers::where('teamid',$id)->delete()){
                    return response()->json(array('error'=>0,'msg'=>'删除成功'));
                }                
                
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }
}