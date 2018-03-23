<?php
namespace App\Http\Controllers\Api\v1d2;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redis;
use App\Helpers\FunctionHelper;
use App\Models\Members;
use Hash;
use Config;
class MemberController extends Controller
{
    private $mid = '';
	public function __construct(Request $request){
		$this->mid = $request->get('mid','');
	}

    //保存个人信息
    public function saveinfo(Request $request){
        $dataArr = array();
        $listArr = Members::where('id','=',$this->mid)->first();

        $icon = $request->get('icon','');
        if(!empty($icon)){            
            $dataArr['icon'] = $icon;
        }

        $name = $request->get('name','');
        if(!empty($name)){ 
            if(mb_strlen($name,'utf8')>8){
                return response()->json(array('error'=>1,'msg'=>'昵称不能超过8个字'));
                exit();
            }

            if(Members::where('name','=',$name)->where('id','!=',$this->mid)->first()){
                return response()->json(array('error'=>1,'msg'=>'昵称已被占用'));
                exit();
            }  

            $dataArr['name'] = $name;
        }

        $idnumber = $request->get('idnumber','');
        if(!empty($idnumber)){
            if(!empty($idnumber) && !FunctionHelper::isCreditNo($idnumber)){
                return response()->json(array('error'=>1,'msg'=>'请填写正确的身份证号'));
                exit();
            }

            $mArr = Members::where('idnumber','=',$idnumber)->first();
            if(!empty($mArr) && $mArr->id!=$this->mid){   
                return response()->json(array('error'=>1,'msg'=>'请填写自己的身份证号'));
                exit();
            }
            
            $dataArr['birthday'] = FunctionHelper::getBirthday($idnumber);
            $dataArr['sex'] = FunctionHelper::getSex($idnumber);
            $dataArr['idnumber'] = $idnumber;
        }

        $address = $request->get('address','');
        if(!empty($address)){
            $addressArr = explode('/',$address);
            if( count($addressArr) < 4 ){
                return response()->json(array('error'=>2,'msg'=>'地址参数错误'));
                exit();
            }

            $dataArr['province']    = (empty($addressArr[0])?'':$addressArr[0]); 
            $dataArr['city']        = (empty($addressArr[1])?'':$addressArr[1]); 
            $dataArr['country']     = (empty($addressArr[2])?'':$addressArr[2]); 
            $dataArr['address']     = (empty($addressArr[3])?'':$addressArr[3]);
        }

        $school = $request->get('school','');
        if(!empty($school)){            
            $dataArr['school'] = $school;
        }

        $position = $request->get('position','');
        if(!empty($position)){            
            $dataArr['position'] = $position;
        }

        $foot = $request->get('foot','');
        if(!empty($foot)){            
            $dataArr['foot'] = $foot;
        }

        $instruction = $request->get('instruction','');
        if(!empty($instruction)){            
            $dataArr['instruction'] = $instruction;
        }

        $heightweight = $request->get('heightweight','');
        if(!empty($heightweight)){
            $heightweightArr = explode('/',$heightweight);
            if( count($heightweightArr) < 2 ){
                return response()->json(array('error'=>2,'msg'=>'身高体重参数错误'));
                exit();
            }
            $dataArr['height']    = (empty($heightweightArr[0])?'':intval($heightweightArr[0])); 
            $dataArr['weight']    = (empty($heightweightArr[1])?'':intval($heightweightArr[1])); 
        }

        $idcard_f = $request->get('idcard_f','');
        if(!empty($idcard_f)){            
            $dataArr['idcard_f'] = $idcard_f;
        }

        $idcard_b = $request->get('idcard_b','');
        if(!empty($idcard_b)){            
            $dataArr['idcard_b'] = $idcard_b;
        }

        $idcard_address = $request->get('idcard_address','');
        if(!empty($idcard_address)){            
            $dataArr['idcard_address'] = $idcard_address;
        }

        $img = $request->get('img','');
        if(!empty($img)){            
            $dataArr['img'] = $img;
        }

        $truename = $request->get('truename','');
        if(!empty($truename)){            
            $dataArr['truename'] = $truename;
        }

        $nation = $request->get('nation','');
        if(!empty($nation)){            
            $dataArr['nation'] = $nation;
        }

        $isauth = $request->get('isauth','');
        if(!empty($isauth)){ 
            $str = '';
            if($listArr->status=='y'){
                unset($dataArr['idnumber']);
                unset($dataArr['truename']);
                unset($dataArr['birthday']);
                unset($dataArr['sex']);
            }

            $str = '';
            if($listArr->status=='n' && $isauth=='y'){
                if($listArr->isauth < 2){
                    if(empty($dataArr['idnumber']) || empty($dataArr['truename']) ){   
                        return response()->json(array('error'=>1,'msg'=>'请填写自己的身份证号和真实姓名'));
                        exit();
                    }

                    if(FunctionHelper::checkidcardnum($dataArr['idnumber'],$dataArr['truename'])){//认证
                        $dataArr['status'] = 'y'; 
                        $str = '认证成功';    
                    }else{
                        $str = '您的还有'.(2-$listArr->isauth).'次认证机会，请检查完整信息';
                        $dataArr['isauth'] = $listArr->isauth+1; 
                    }
                }else{
                    $str = '您的认证机会已使用完,请联系客服010-57159820';    
                }
            }
        }

        if(empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'无任何修改'));
            exit();
        }

        
       
        if(Members::where('id','=',$this->mid)->update($dataArr)){ 
            return response()->json(array('error'=>0,'msg'=>'修改成功'.(empty($str)?'':';'.$str) ));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'修改失败'));

    }

    

}