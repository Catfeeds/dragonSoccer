<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Models\Webconfig;
use Input;
use DB;
use App\Helpers\FunctionHelper;
use App\Helpers\OssUploadHelper;

class WebconfigController extends Controller
{
    private $bucket = 'lzsn-icon';
    private $dir = 'webconfig/';
	public $webArr = array('android'=>'安卓版本','ios'=>'IOS版本','indexschoolimg'=>'首页校园赛事图片','footballage'=>'龙少赛事年龄段','indexgamesid'=>'首页赛事ID','footballcashimg'=>'奖金池背景图','appindeximg'=>'app启动页图');

    public function index(){
    	$data['android'] = Webconfig::where('key','=','android')->first();
        $data['ios'] = Webconfig::where('key','=','ios')->first();
        $data['indexschoolimg'] = Webconfig::where('key','=','indexschoolimg')->first();
        $data['footballage'] = Webconfig::where('key','=','footballage')->first();
        $data['indexgamesid'] = Webconfig::where('key','=','indexgamesid')->first();
        $data['footballcashimg'] = Webconfig::where('key','=','footballcashimg')->first();
        $data['appindeximg'] = Webconfig::where('key','=','appindeximg')->first();
        
    	return view('admin.webconfig_index',$data)->with('webArr',$this->webArr)->with('ossconfig',OssUploadHelper::makeConfig($this->bucket,$this->dir));
    }

    public function ajaxsaveval(Request $request){
    	$val  = $request->get('val','');
        $key  = $request->get('key','');
        $r = Webconfig::where('key','=',$key)->first();
        $rr = false;

        try {
            if(empty($r)){
                $r1 = Webconfig::create(array('key'=>$key,'val'=>$val));
            }else{
                $r1 = Webconfig::where('key','=',$key)->update(array('val'=>$val));
            }

            if(!empty($r1)){
                return response()->json(array('error'=>0,'msg'=>'成功'));
                exit();
            }        
        } catch (\Exception $e) {
                
        }

    	return response()->json(array('error'=>1,'msg'=>'失败'));
    }
}
