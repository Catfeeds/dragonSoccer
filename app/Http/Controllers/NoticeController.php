<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Helpers\FunctionHelper;
use App\Models\Notice;
use Config;
use DB;
class NoticeController extends Controller{

    public function __construct(){

    }
    
    public function info($id){
        $listArr = Notice::where('id','=',$id)->first();
        return view('front.notice_info')->with('listArr',$listArr);
    }
  
}
