<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Models\Banner;
use App\Helpers\OssUploadHelper;
use Config;
use DB;
class BannerController extends Controller{
    
    private $bucket = 'lzsn-icon';
    private $dir = 'banner/';

    public function __construct(){

    }

    public function index() {
        $listArr = Banner::orderBy('id', 'desc')->paginate(20);        
        return view('admin.banner_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.banner_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写标题'));
        }
        if(empty($input['img'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }

        if(empty($input['url'])){
            return response()->json(array('error'=>1,'msg'=>'请填写跳转链接'));
        }
        
        if(Banner::create($input)){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.banner.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Banner::where('id','=',$id)->first();
        }
        return view('admin.banner_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','name','img','url','status','sid','sharecontent'); 
        $id = $input['id'];
        unset($input['id']);
        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['img'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
        }

        if(empty($input['url'])){
            return response()->json(array('error'=>1,'msg'=>'请填写跳转链接'));
        }
        
        $res = Banner::where('id','=',$id)->update($input);
        if($res){
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.banner.index')));     
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(!empty(Banner::destroy($id))){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 


}
