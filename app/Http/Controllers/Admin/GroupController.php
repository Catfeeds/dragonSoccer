<?php
namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

use App\Models\Area;
use App\Models\Games;
use App\Models\Gamesages;
use App\Models\Group;
use App\Models\Groupmembers;
use App\Models\Members;
use Config;
use DB;
class GroupController extends Controller{
    public function __construct(){

    }

    public function index() {
        $listArr = Group::with('gamesages','gamesages.games')->orderBy('id', 'desc')->paginate(20);        
        return view('admin.group_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        $gamesArr = Games::orderBy('sid', 'desc')->get();        
        return view('admin.group_add')->with('gamesArr',$gamesArr);
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

    public function ajaxmember(Request $request){  
        $gaid = $request->get('gaid','');
        $page = $request->get('page','1');
        $ageArr = Gamesages::where('id','=',$gaid)->first();
        $membersArr = [];
        if(!empty($ageArr)){
            $membersArr = Members::where('birthday','>=',date('Y-m-d',$ageArr->starttime))->where('birthday','<=',date('Y-m-d',$ageArr->endtime))->orderBy('id', 'desc')->paginate(16);
            //$membersArr = Members::orderBy('id', 'desc')->paginate(16);
        } 
        $allpage = empty($membersArr)?1:$membersArr->lastPage();      
        return view('admin.group_ajaxmember')->with('membersArr',$membersArr)->with('page',$page)->with('allpage',$allpage);
    }

    public function ajaxadd(Request $request){
        $input = $request->all();
        try { 
            $p = '';
            $c = '';
            if(count($input['mids'])>0){
                $mArr = Members::where('id',$input['mids'][0])->first();
                $p = $mArr->province;
                $c = ($mArr->city=='市辖区'||$mArr->city=='县')?$mArr->country:$mArr->city;
            }else{
                return response()->json(array('error'=>1,'msg'=>'请选择成员'));
                exit();    
            }
            $res = false;            
            DB::beginTransaction();
                $r1 = Group::create(['gamesagesid'=>$input['gamesagesid'],'number'=>count($input['mids']),'province'=>$p,'city'=>$c ]);
                foreach ($input['mids'] as $k => $v) {
                    Groupmembers::create(['groupid'=>$r1->id,'mid'=>$v,'isleader'=>$k==0?'y':'n' ]);    
                }
                
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.group.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }
                
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
        exit();
    }

    public function edit(Request $request){
        $id = $request->get('id','');
        $gArr = Group::with('gamesages','gamesages.games')->find($id);
        $gmArr = Groupmembers::with('members')->where('groupid',$id)->get();
        return view('admin.group_edit')->with('gArr',$gArr)->with('gmArr',$gmArr);
    } 


    public function ajaxdelmember(Request $request){
        $id = $request->get('id','');
        $ga = Groupmembers::find($id);
        if(Groupmembers::destroy((int)$id)){
            Group::where('id','=',$ga->groupid)->decrement('number',1); 
            return response()->json(array('error'=>0,'msg'=>'删除成功','url'=>route('admin.group.index')));
            exit();     
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
        exit();
    }

    public function ajaxaddmember(Request $request){
        $id = $request->get('id','');
        $mids = $request->get('mids','');
        try { 
            $res = false;            
            DB::beginTransaction();
            if(count($mids)>0){
                foreach ($mids as $k => $v) {
                    Groupmembers::create(['groupid'=>$id,'mid'=>$v]);    
                }
            }
            Group::where('id','=',$id)->increment('number',count($mids)); 
            $res = true;
            DB::commit();  
            if($res){
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.group.index')));
                exit();     
            }     
        } catch (Exception $e) {   

        }

        return response()->json(array('error'=>1,'msg'=>'删除失败'));
        exit();
    }
    

    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(Group::destroy((int)$id)){
                if(Groupmembers::where('groupid',$id)->delete()){
                    return response()->json(array('error'=>0,'msg'=>'删除成功'));
                }                
                
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }
}
