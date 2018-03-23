<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Models\Notice;
use Config;
use DB;
class NoticeController extends Controller{
    
    public $noticeArr = array();
    public function __construct(){
        $this->noticeArr = Config::get('custom.notice');
    }

    public function index() {
        $listArr = Notice::orderBy('id', 'desc')->paginate(20);        
        return view('admin.notice_index')->with('listArr',$listArr)->with('noticeArr',$this->noticeArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.notice_add');
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        
        if(empty($input['title'])){
            return response()->json(array('error'=>1,'msg'=>'请填写标题'));
        }

        if(empty($input['content'])){
            return response()->json(array('error'=>1,'msg'=>'请填写赛事规则'));
        }
        
        if(Notice::create($input)){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.notice.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Notice::where('id','=',$id)->first();
        }
        return view('admin.notice_edit')->with('listArr',$listArr);
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','title','content','status','rsort'); 
        $id = $input['id'];
        unset($input['id']);
        if(empty($input['title'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['content'])){
            return response()->json(array('error'=>1,'msg'=>'请填写赛事规则'));
        }
        
        $res = Notice::where('id','=',$id)->update($input);
        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.notice.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(!empty(Notice::destroy($id))){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 


    //获取数据   
    public function ajaxupdate(Request $request){
        $id = $request->get('id','');
        $val = $request->get('val','');
        if(empty($val)){
            return response()->json(array('error'=>1,'msg'=>'请填写排序值'));
        }
        $res = Notice::where('id','=',$id)->update(array('rsort'=>$val));
        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.notice.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }
  
}
