<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;
use Session;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Adminusers;
use Hash;
class AdminuserController extends Controller{
    private $bucket = 'lzsn-icon';
    private $dir = 'default/';

    public function index() {
        $listArr = Adminusers::orderBy('id', 'desc')->paginate(20);        
        return view('admin.adminuser_index')->with('listArr',$listArr);
    }

    //获取数据   
    public function add(){        
        $listArr = Role::orderBy('id', 'desc')->get();
        return view('admin.adminuser_add')->with('listArr',$listArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
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

        if(empty($input['jobtitle'])){
            return response()->json(array('error'=>1,'msg'=>'请填写职位'));
        }

        if(empty($input['sex'])){
            return response()->json(array('error'=>1,'msg'=>'请选择性别'));
        }

        if(empty($input['birthday'])){
            return response()->json(array('error'=>1,'msg'=>'请填写出生年月'));
        }

        if(empty($input['password'])){
            return response()->json(array('error'=>1,'msg'=>'请填写密码'));
        }

        if(empty($input['mobile']) || !FunctionHelper::isMobile($input['mobile'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
        }

        if($input['password2'] != $input['password2'] ){
            return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
        }
        unset($input['password2']);
        $input['password']=Hash::make($input['password']);       
               
        if(!Adminusers::where('mobile','=',$input['mobile'])->first()){
            if($r = Adminusers::create($input)){
                if (is_array($request->get('rids'))) {
                    if(Adminusers::find($r->id)->roles()->sync($request->get('rids',[]) )){
                        return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.adminuser.index') ));
                    }
                }
                return response()->json(array('error'=>0,'msg'=>'添加成功','url'=>route('admin.adminuser.index') ));                
            }
        }        
        return response()->json(array('error'=>1,'msg'=>'添加失败'));
    }    

    //获取数据   
    public function edit(Request $request){
        $id = $request->get('id','');
        $rolesArr = array();
        $roleallArr = array();
        if(!empty($id)){
            if($listArr = Adminusers::with('roles')->where('id','=',$id)->first()){                
                if(!empty($listArr->roles)){
                    foreach ($listArr->roles as $v) {
                        $rolesArr[] = $v->id;   
                    }
                }
                $roleallArr = Role::orderBy('id', 'desc')->get();
            }
        }
        return view('admin.adminuser_edit')->with('listArr',$listArr)->with('roleallArr',$roleallArr)->with('rolesArr',$rolesArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    //获取数据   
    public function ajaxedit(Request $request){
        $input = $request->only('id','name', 'mobile', 'jobtitle', 'password','icon','sex','birthday'); 
        $id = $input['id'];
        unset($input['id']);
        $rids = $request->get('rids','');
        unset($input['rids']);
        unset($input[$request->path()]); 

        if(empty($input['icon'])){
            return response()->json(array('error'=>1,'msg'=>'请上传头像'));
        }

        if(empty($input['name'])){
            return response()->json(array('error'=>1,'msg'=>'请填写名称'));
        }

        if(empty($input['jobtitle'])){
            return response()->json(array('error'=>1,'msg'=>'请填写职位'));
        }

        if(empty($input['sex'])){
            return response()->json(array('error'=>1,'msg'=>'请选择性别'));
        }

        if(empty($input['birthday'])){
            return response()->json(array('error'=>1,'msg'=>'请填写出生年月'));
        }

      
        if(empty($input['mobile']) || !FunctionHelper::isMobile($input['mobile'])){
            return response()->json(array('error'=>1,'msg'=>'请填写正确的手机号'));
        }      
        
        if(empty($input['password'])){
            unset($input['password']);
            unset($input['password2']);
        }else{
            if($input['password2'] != $input['password2'] ){
                return response()->json(array('error'=>1,'msg'=>'两次输入密码不一致'));
            }
            unset($input['password2']);
            $input['password']=Hash::make($input['password']);
        }   
        if(Adminusers::where('id','=',$id)->update($input)){  
            if (is_array($rids)) {
                if(Adminusers::find($id)->roles()->sync($rids)){
                    return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.adminuser.index') ));
                }
            }
            return response()->json(array('error'=>0,'msg'=>'修改成功','url'=>route('admin.adminuser.index') ));
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));
    }


    public function ajaxdel(Request $request){   
        $id = $request->get('id','');
        if(!empty($id)){ 
            $uObj = Adminusers::find((int)$id);
            if(!empty($uObj->roles)){
                foreach ($uObj->roles as $v) {
                    $uObj->roles()->detach($v);
                }
            }            
            if ($uObj && $uObj->id != 1) {
                $r = $uObj->delete();
            }        
            if(!empty($r)){                
                return response()->json(array('error'=>0,'msg'=>'删除成功'));
            }            
        }
        return response()->json(array('error'=>1,'msg'=>'删除失败'));
    } 
  
}
