<?php

namespace App\Http\Controllers\Api\v1;
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


    public function alist() {
        $listArr = Notice::where('status','=','y')->orderBy('rsort', 'desc')->orderBy('id', 'desc')->limit(6)->get();
        $dataArr = array();
        if(!empty($listArr)){
            foreach ($listArr as $k => $v) {
                $arr = array();   
                $arr['title']    = $v->title;    
                $arr['infourl'] = url('/notice/info/'.$v->id);
                $dataArr[] = $arr;
            }
        }

        if(!empty($dataArr)){
            return response()->json(array('error'=>0,'msg'=>'成功','data'=>$dataArr));
            exit();
        }
        return response()->json(array('error'=>1,'msg'=>'暂无数据'));
    }

    
  
}
