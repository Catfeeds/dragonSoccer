<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\OssUploadHelper;
use App\Models\Activityapply;
use App\Models\Activityvote;
use Config;
class ActivityapplyController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'activity/';
    private $activityArr = array();    
    public function __construct(Request $request){
        $this->activityArr = Config::get('custom.activityArr');   
    }

    public function index(Request $request) {
        $listArr = Activityapply::with('member')->orderBy('id', 'desc')->paginate(20);
        return view('admin.activityapply_index')->with('listArr',$listArr)->with('activityArr',$this->activityArr);
    }

    public function vote(Request $request) {
        $listArr = Activityvote::with('member','bestmember')->where(['type'=>'spokesman'])->orderBy('id', 'desc')->paginate(20);
        return view('admin.activityapply_vote')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.activityapply_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $txt = $request->get('txt','');
        $imgs = $request->get('imgs','');
        $mid = $request->get('mid','');
        $status = $request->get('status','w');
        if(empty($imgs)){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
            exit();
        }

        if(empty($mid)){
            return response()->json(array('error'=>1,'msg'=>'请填写会员id'));
            exit();
        }

        if(empty($txt)){
            return response()->json(array('error'=>1,'msg'=>'请填写文字'));
            exit();
        }
        
        if($r = Activityapply::create(['imgs'=>json_encode($imgs),'txt'=>$txt,'mid'=>$mid,'status'=>$status])){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.activityapply.index') ));                
        }  
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Activityapply::where('id','=',$id)->first();
            $listArr->imgArr = empty($listArr->imgs)?array():json_decode($listArr->imgs,true);
        }
        return view('admin.activityapply_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->all(); 
        $id = $input['id'];
        unset($input['id']);

        $txt = $request->get('txt','');
        $imgs = $request->get('imgs','');
        $mid = $request->get('mid','');
        $status = $request->get('status','w');
        $remark = $request->get('remark','');
        if(empty($imgs)){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
            exit();
        }

        if(empty($mid)){
            return response()->json(array('error'=>1,'msg'=>'请填写会员id'));
            exit();
        }

        if(empty($txt)){
            return response()->json(array('error'=>1,'msg'=>'请填写文字'));
            exit();
        }

        if($status=='n' && empty($remark)){
            return response()->json(array('error'=>1,'msg'=>'请填写备注'));
            exit();
        }

        if(Activityapply::where('id','=',$id)->update(['imgs'=>json_encode($imgs),'txt'=>$txt,'mid'=>$mid,'status'=>$status,'remark'=>$remark])){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.activityapply.index') ));
        }

        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }
    
}
