<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;
use App\Models\Members;
use App\Models\Match;
use App\Models\Relation;
use App\Models\Comment;
use App\Models\Apply;
use App\Models\Applyinvite;
use App\Helpers\EasemobHelper;
use App\Models\Team;
use App\Models\Teammember;
use App\Models\Area;

use Hash;
use DB;
use Config;
class TeamController extends Controller
{
    private $teamArr = '';
    private $memberArr = '';
    private $systemArr = '';
    private $bucket = 'lzsn-icon';
    private $dir = 'default/';
	public function __construct(Request $request){
        $this->teamArr = Config::get('custom.team');
        $this->memberArr = Config::get('custom.member');
        $this->systemArr = Config::get('custom.system');
	}

    public function index() {        
        $listArr = Team::with('match')->where('type','=','m')->orderBy('id', 'desc')->paginate(20);
        return view('admin.team_index')->with('listArr',$listArr)->with('type','m')->with('teamArr',$this->teamArr);
    }

    public function alist() {
        $listArr = Team::where('type','=','f')->orderBy('id', 'desc')->paginate(20);
        return view('admin.team_index')->with('listArr',$listArr)->with('type','f');
    }
    

    //获取数据   
    public function add(Request $request){ 
        $type = $request->get('type','f');
        $provinceArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
        $matchArr = Match::with('info')->orderBy('id', 'desc')->paginate(20);         
        return view('admin.team_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('type',$type)->with('matchArr',$matchArr)->with('provinceArr',$provinceArr);
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传头像'));
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if($input['type']=='m' && empty($input['matchid'])){
            return response()->json(array('error'=>1,'msg'=>'请选择比赛'));
        }

        $city = $input['city'];
        if(in_array($input['province'],array('北京','天津','上海','重庆'))){
            $city = $input['country'];
        }
        
        if($r = Team::create(array('icon'=>$input['icon'],'name'=>$input['name'],'province'=>$input['province'],'city'=>$city,'type'=>$input['type'],'matchid'=>empty($input['matchid'])?'':$input['matchid'] ) )){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.team.'.($input['type']=='m'?'index':'alist') ) ));                
        }   
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        $matchArr = array();
        if(!empty($id)){
            $matchArr = Match::with('info')->orderBy('id', 'desc')->paginate(20);
            $listArr = Team::with('teammember','teammember.member','match')->where('id','=',$id)->first();
            //var_dump($listArr->teammember->toArray());
        }
        $provinceArr = Area::where(array('parentid'=>'0'))->where('code','<','71')->orderBy('id', 'asc')->get();
        return view('admin.team_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir))->with('matchArr',$matchArr)->with('provinceArr',$provinceArr);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->all();
        $id = $input['id'];
        unset($input['id']);

        $teamArr = Team::with('teammember')->where('id','=',$id)->first();

        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传头像'));
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if($input['type']=='m' && empty($input['matchid'])){
            return response()->json(array('error'=>1,'msg'=>'请选择比赛'));
        }

        $city = $input['city'];
        if(in_array($input['province'],array('北京','天津','上海','重庆'))){
            $city = $input['country'];
        }

        $res = false; 
        DB::beginTransaction();
            Team::where('id','=',$id)->update(array('icon'=>$input['icon'],'name'=>$input['name'],'province'=>$input['province'],'city'=>$city,'type'=>$input['type'],'matchid'=>empty($input['matchid'])?'':$input['matchid'] ) );

            $ownid = '';
            $teamMemberName = '';
            $easemobGids = '';
            if(!empty($input['mids'])){
                foreach ($input['mids'] as $k => $mid){
                    $midArr = Members::where('id','=',$mid)->first();
                    $insertArr = array('isleader'=>'n');
                    if(empty($teamArr->teammember)){
                        $insertArr['isleader'] = $i>0?'y':'n';
                        $ownid = $mid;
                    }
                    $insertArr['mid'] = $mid;   
                    $insertArr['name'] = $midArr->name;
                    $insertArr['teamid'] = $id;
                    Teammember::create($insertArr);

                    $teamMemberName .= $midArr->name."|" ;
                    $easemobGids[] = $this->memberArr['easemobArr']['member'].$mid; 
                    
                }

                $gid = $teamArr->gid;
                if(empty($teamArr->gid)){
                    if($gid = EasemobHelper::createGroups($this->memberArr['easemobArr']['group'].$id,$input['name'],$ownid,$easemobGids)){
                        Team::where(array('id'=>$id))->update(array('gid'=>$gid));
                    }
                }

                EasemobHelper::addUser($this->systemArr['easemobArr']['addgroup'],md5($this->systemArr['easemobArr']['addgroup']),$this->systemArr['easemobArr']['addgroup']); //环信
                EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],$easemobGids,$this->systemArr['easemobmsgArr']['addgroup']); //环信

                EasemobHelper::sendMsg($this->systemArr['easemobArr']['addgroup'],array($gid),$teamMemberName,$this->systemArr['easemobtypeArr']['group']); //环信  
            }

            $res = true;  
        DB::commit();

        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.team.'.($input['type']=='m'?'index':'alist')) ));
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }

    //会员所有数据 
    public function ajaxgetmember(Request $request) {
        $page = $request->get('page','1');
        $listArr = Members::whereNotIn('id', function ($query) {
                    $query->select('mid')->from('team_member');
                })->orderBy('id', 'desc')->paginate(10); 

         //var_dump($listArr); 
         
        return view('admin.team_ajaxgetmember')->with('listArr',$listArr)->with('curpage',$page);
    }

}