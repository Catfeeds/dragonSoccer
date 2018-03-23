<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\OssUploadHelper;
use App\Models\Goods;

class GoodsController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'goods/';
    public function __construct(){
    }

    public function index(Request $request) {
        $listArr = Goods::orderBy('id', 'desc')->paginate(20);
        return view('admin.goods_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        return view('admin.goods_add')->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxadd(Request $request){
        $input = $request->all();
        if(empty($input['img'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
            exit();
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
            exit();
        }

        if(empty($input['appleid'])){
            return response()->json(array('error'=>1,'msg'=>'请选择类型'));
            exit();
        }
        if($input['appleid']=='android'){
            $input['number'] = 1;
            unset($input['apple']);
        }

        if($input['appleid']=='ios'){
            $input['appleid']= $input['apple'];
            unset($input['apple']);
            if(empty($input['number'])){
                return response()->json(array('error'=>1,'msg'=>'请填写数量'));
                exit();
            }
        }

        if(empty($input['price'])){
            return response()->json(array('error'=>1,'msg'=>'请填写价格'));
        }

        if($r = Goods::create($input)){
            return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.goods.index') ));                
        }  
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $listArr = array();
        if(!empty($id)){
            $listArr = Goods::where('id','=',$id)->first();
        }
        return view('admin.goods_edit')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->all(); 
        $id = $input['id'];
        unset($input['id']);

        if(empty($input['img'])){
            return response()->json(array('error'=>1,'msg'=>'请上传图片'));
            exit();
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
            exit();
        }

        if(empty($input['appleid'])){
            return response()->json(array('error'=>1,'msg'=>'请选择类型'));
            exit();
        }
        if($input['appleid']=='android'){
            $input['number'] = 1;
        }

        if($input['appleid']=='ios'){
            $input['appleid']= $input['apple'];
            unset($input['apple']);
            if(empty($input['number'])){
                return response()->json(array('error'=>1,'msg'=>'请填写数量'));
                exit();
            }
        }

        if(empty($input['price'])){
            return response()->json(array('error'=>1,'msg'=>'请填写价格'));
        }

        $dataArr['img'] = $input['img']; 
        $dataArr['name'] = $input['name']; 
        $dataArr['appleid'] = $input['appleid']; 
        $dataArr['number'] = $input['number']; 
        $dataArr['price'] = $input['price']; 
        $dataArr['url'] = empty($input['url'])?'':$input['url']; 
        $dataArr['status'] = empty($input['status'])?'n':$input['status'];
       
        if(Goods::where('id','=',$id)->update($dataArr)){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.goods.index') ));
        }

        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){
            if(Goods::destroy((int)$id)){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    }
}
