<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::get('/',function(){
	phpinfo();die;
});


Route::group(['prefix'=>'match'], function () {
	Route::get('info/{id}','MatchController@info');
});

Route::group(['prefix'=>'notice'], function () {
	Route::get('info/{id}','NoticeController@info'); 
});

Route::group(['prefix'=>'area'], function () {
	Route::get('ajaxgetlist/{fid?}','AreaController@ajaxgetlist'); 
});

Route::get('downloapak/{sts?}',function($sts=''){
	if($sts=='s'){
		ob_start();
		header("Location:http://a.app.qq.com/o/simple.jsp?pkgname=com.dragonfb.dragonball");
		exit();
	}
	return view('front.downIosForAndroid');
});


//活动
Route::group(['prefix'=>'activity'], function () {	
	Route::any('index','ActivityController@index');//首页
	Route::any('spokesman/list','ActivityController@spokesmanlist'); //代言人
	Route::any('spokesman/detail/{sex}','ActivityController@spokesmandetail');
});

Route::group(['prefix'=>'activity','middleware' => ['webauth']], function () {
	Route::any('spokesman/apply','ActivityController@spokesmanapply'); //报名
	Route::any('spokesman/ajaxapply','ActivityController@ajaxspokesmanapply'); //报名申请
	Route::any('spokesman/ajaxsupport','ActivityController@ajaxspokesmansupport'); //报名申请
	Route::any('spokesman/info/{id}','ActivityController@spokesmandinfo'); //详情

	Route::any('sharecash','ActivityController@sharecash'); //分享现金
	Route::any('sharecashinfo','ActivityController@sharecashinfo');//分享说明
	Route::any('withdraw','ActivityController@withdraw'); //提现
	Route::any('ajaxwithdraw','ActivityController@ajaxwithdraw');
	Route::any('withdrawinfo','ActivityController@withdrawinfo');//提现明细
});


Route::group(['prefix'=>'txt'], function () {
	Route::get('register',function(){
		return view('front.txt_register');
	}); 

	Route::get('infoauth',function(){
		return view('front.txt_infoauth');
	});

	Route::get('rule7',function(){
		return view('front.txt_rule7');
	}); 

	Route::get('rule2017',function(){
		return view('front.txt_rule2017');
	});

	Route::get('cash',function(){
		return view('front.txt_cash');
	});

	//注册送现金
	Route::get('regcash',function(){
		return view('front.txt_regcash');
	});

	Route::get('video/{id}',function($id){
		return view('front.txt_video')->with('id',$id);
	});

	//龙珠说明
	Route::get('longzhu',function(){
		return view('front.txt_longzhu');
	});


	Route::get('activity',function(){
		return view('front.txt_activity');
	});

	Route::get('spokesman',function(){
		return view('front.txt_spokesman');
	});

	Route::get('lsruler',function(){
		return view('front.txt_lsruler');
	});

	Route::get('group',function(){
		return view('front.txt_group');
	});
});

//注册 11-1
Route::get('invite','CommonController@invite');
Route::get('share','CommonController@share');
Route::any('ajaxsignin','CommonController@ajaxsignin');

Route::any('schoolinfo','CommonController@schoolinfo'); //校园赛事详情 01-17


//支付回掉
Route::any('alinotify','PayController@alinotify');
Route::any('wechatnotify','PayController@wechatnotify');

//环信 11-22
Route::group(['prefix'=>'webim'], function () {
    Route::get('login','WebimController@login');
    Route::post('ajaxlogin', 'WebimController@ajaxlogin');
    Route::group(['middleware' => ['webimauth']], function () {
        Route::get('logout','WebimController@logout');
        Route::get('chat','WebimController@chat');
        Route::get('ajaxsetchat','WebimController@ajaxsetchat');
        Route::get('ajaxgetchat','WebimController@ajaxgetchat');
        Route::get('ajaxgetmember','WebimController@ajaxgetmember');
        Route::get('ajaxgetmemberbymobile','WebimController@ajaxgetmemberbymobile');        
        Route::get('ajaxsetownerchat','WebimController@ajaxsetownerchat');        

        Route::get('logout', 'WebimController@logout');
    });
});

Route::get('test1',function(){
	$r = EasemobHelper::chatGroupsDetails('39838754603009');
	//var_dump(date('y-m-d H:i:s',1517392493931));
	var_dump(date('y-m-d H:i:s',time()));
	var_dump($r);
});
