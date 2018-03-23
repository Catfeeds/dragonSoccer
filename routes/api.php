<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::group(['prefix'=>'v1','namespace' => '\v1'], function () {	
	//赛事
	Route::any('match/getlist','MatchController@alist'); 
	Route::any('match/getone','MatchController@single');

	Route::any('banner/list','CommonController@bannerlist');//首页banner 10-27
	Route::any('dayprocess/list','CommonController@dayprocesslist'); //首页今日赛程 10-27
	//Route::any('cash/getallnum','CommonController@cashgetallnum'); //首页奖金池 10-27
	Route::any('cash/list','CommonController@cashlist'); //奖金池列表 10-27

	Route::any('matchlog/getall','MatchlogController@getall');//赛程安排 1109
	Route::any('matchlog/getallbydate','MatchlogController@getallbydate');//赛程安排日历 1109
	Route::any('matchlog/getallbyprovince','MatchlogController@getallbyprovince');//赛程安排赛区 1109

	Route::any('match/levellist','MatchController@levellist');//今日赛程-比赛类别 1114
	Route::any('matchlog/getallbyday','MatchlogController@getallbyday');//今日赛程安排 1114
	Route::any('matchlog/getone','MatchlogController@getone');//赛程详情 1114	

	Route::any('matchlog/matchrankcity','MatchlogController@matchrankcity');//排行榜 按省市 1109
	Route::any('matchlog/matchrankprovince','MatchlogController@matchrankprovince');//排行榜 按省 1109
	Route::any('matchlog/matchrankcountry','MatchlogController@matchrankcountry');//排行榜 按省 1109
	Route::any('matchlog/matchrankinfo','MatchlogController@matchrankinfo');//排行榜 详情 1109

	Route::any('matchlog/matchrankteam','MatchlogController@matchrankteam');//队伍排行榜 详情 1109
	Route::any('matchlog/matchrankteaminfo','MatchlogController@matchrankteaminfo');//队伍排行榜 详情 1109

	//公告
	Route::any('notice/getlist','NoticeController@alist');

	//发送短信 短信检测 注册  登陆  短信登陆  忘记密码
	Route::any('sendmessage','ApiController@sendmessage');
	Route::any('checkmessage','ApiController@checkmessage');
	Route::any('signin','ApiController@signin');
	Route::any('login','ApiController@login');
	Route::any('logincode','ApiController@logincode');
	Route::any('forgetpwd','ApiController@forgetpwd');

	Route::any('getconfigposition','ApiController@getconfigposition');

	//获取版本号
	Route::any('getandroid/version','CommonController@getandroidversion');
	Route::any('getios/version','CommonController@getiosversion');


	//商品信息 v1.3
	Route::any('goods/listforios','GoodsController@goodslistforios');
	Route::any('goods/listforandroid','GoodsController@goodslistforandroid');

	Route::any('member/getalisign','MemberController@getalisign');//获取阿里签名
	
	Route::group(['prefix'=>'member','middleware' => ['apiauth']], function () {
		//用户
		Route::any('infodata','MemberController@infodata');//用户各种组合 12-05
		Route::any('allmsg','MemberController@allmsg');
		Route::any('updatepwd','MemberController@updatepwd');
		Route::any('getone','MemberController@single');
		Route::any('view','MemberController@viewsingle');
		Route::any('getconfig','MemberController@getconfig');
		Route::any('saveinfo','MemberController@saveinfo');
		Route::any('updateshow','MemberController@updateshow');
		Route::any('savemsgtatus','MemberController@savemsgtatus');
		Route::any('loginbymid','MemberController@loginbymid');//h5 登陆 18-01-04

		//意见反馈
		Route::any('addcomment','MemberController@addcomment');

		//收藏
		Route::any('getcollectlist','MemberController@getcollectlist');
		Route::any('addcollect','MemberController@addcollect');
		Route::any('delcollect','MemberController@delcollect');

		//举报
		Route::any('getwarningreason','MemberController@getwarningreason');
		Route::any('addwarning','MemberController@addwarning');
		//好友举报
		Route::any('getmemberwarningreason','MemberController@getmemberwarningreason');
		Route::any('addmemberwarning','MemberController@addmemberwarning');
		
		//好友搜索
		Route::any('searchlist','MemberController@searchlist');

		//好友关系
		Route::any('listrelation','MemberController@listrelation');
		Route::any('applyrelation','MemberController@applyrelation');
		Route::any('listapplyrelation','MemberController@listapplyrelation');
		Route::any('loserelation','MemberController@loserelation');
		Route::any('acceptrelation','MemberController@acceptrelation');
		Route::any('delrelation','MemberController@delrelation');

		//参赛
		Route::any('applymatch','ApplyController@applymatch'); //报名
		Route::any('listapplymatch','ApplyController@listapplymatch'); //报名 列表
		Route::any('addapplyinvite','ApplyController@addapplyinvite'); //邀请好友
		Route::any('listapplyinvite','ApplyController@listapplyinvite'); //邀请好友 列表
		Route::any('loseapplyinvite','ApplyController@loseapplyinvite'); //邀请好友 拒绝
		Route::any('loseapply','ApplyController@loseapply'); //退出队伍 解散
		Route::any('delapplymember','ApplyController@delapplymember'); //移除队伍
		Route::any('acceptapplyinvite','ApplyController@acceptapplyinvite'); //邀请好友 同意
		Route::any('updateapplyposition','ApplyController@updateapplyposition'); //邀请好友 修改位置
		Route::any('startapplymatch','ApplyController@startapplymatch'); //开始匹配
		Route::any('stopapplymatch','ApplyController@stopapplymatch'); //终止匹配

		Route::any('listapply','ApplyController@listapply'); //我的比赛
		Route::any('listapplyinfo','ApplyController@listapplyinfo'); //比赛详情


		//群
		Route::any('listteam','TeamController@listteam');
		Route::any('listteammember','TeamController@listteammember');
		Route::any('teaminfo','TeamController@listteaminfo');

		Route::any('creatteam','TeamController@creatteam');
		Route::any('teamlistrelation','TeamController@teamlistrelation');
		Route::any('addteammember','TeamController@addteammember');
		Route::any('delteammember','TeamController@delteammember');
		Route::any('quitteammember','TeamController@quitteammember'); 
		Route::any('changeteamleader','TeamController@changeteamleader');

		Route::any('updateteamicon','TeamController@updateteamicon'); 
		Route::any('updateteamname','TeamController@updateteamname'); 
		Route::any('updatemembername','TeamController@updatemembername'); 
		Route::any('updateisshowmsg','TeamController@updateisshowmsg'); 
		Route::any('updateisshowname','TeamController@updateisshowname'); 
		Route::any('updatenumber','TeamController@updatenumber'); //修改球衣编号  10-27

		//我的赛程 11-10
		Route::any('matchloglist','MymatchlogController@alist');//赛程安排 1109
		Route::any('matchlogchose','MymatchlogController@chosedateaddress');//日期场地选择 1109
		Route::any('matchlogchosesave','MymatchlogController@savechosedateaddress');//日期场地保存 1109
		Route::any('matchlogsaveresult','MymatchlogController@saveresult');//赛事结果保存 1109
		Route::any('matchlogteammember','MymatchlogController@teammember');//所有队员 1115
		Route::any('matchlogvotesave','MymatchlogController@votesave');//所有队员 1115
		Route::any('matchlogstart','MymatchlogController@startmatchlog');//所有队员 1115

		//订单 1201
		Route::any('addforios','OrdersController@addforios'); //ios订单
		Route::any('paymentforios','OrdersController@paymentforios'); //ios凭证
		Route::any('paymentforandroid','OrdersController@paymentforandroid');
		Route::any('orderlistforios','OrdersController@mylistforios'); //ios我的订单
		Route::any('orderlistforandroid','OrdersController@mylistforandroid'); //安卓我的订单
		Route::any('orderinfo','OrdersController@myinfo'); //订单详情		
		Route::any('balanceloglist','MemberController@balanceloglist'); //龙珠日志详情

		//阿里登陆
		//Route::any('getalisign','MemberController@getalisign');//获取阿里签名
		Route::any('savealiwechat','MemberController@savealiwechat');//保存阿里 微信
		
		//登陆送龙珠
		Route::any('balancelogadd','MemberController@balancelogadd');

		//退出登陆
		Route::any('logout','MemberController@logout');
		

	});

});

//10-27
Route::group(['prefix'=>'v1.2','namespace' => '\v1d2'], function () {
	Route::any('match/getlist','MatchController@alist'); //赛事

	//用户
	Route::group(['prefix'=>'member','middleware' => ['apiauth']], function () {
		Route::any('saveinfo','MemberController@saveinfo');//用户信息修改
	});
});

//2.0 18-1-12
Route::group(['prefix'=>'v2','namespace' => '\v2'], function () {
	Route::any('bannerlist','CommonController@bannerlist');
	Route::any('gameinfo','CommonController@gameinfo');
	Route::any('gamecontent','CommonController@gamecontent');//龙少详情
	Route::any('getandroid/version','CommonController@getandroidversion');
	Route::any('getios/version','CommonController@getiosversion');
	Route::any('gameschool','CommonController@gameschool');
	Route::any('gameschoolcontent','CommonController@gameschoolcontent');//龙少详情
	Route::any('getappimg','CommonController@getappimg');


	//用户
	Route::group(['prefix'=>'member','middleware' => ['apiauth']], function () {
		Route::any('searchlist','MemberController@searchlist');//用户信息修改
		Route::any('searchlistteam','MemberController@searchlistteam');//用户信息修改

		Route::any('infodata','MemberController@infodata');//用户各种组合 12-05
		Route::any('allmsg','MemberController@allmsg');//用户各种组合 12-05
		Route::any('getcollectlist','MemberController@getcollectlist');
		
		Route::any('kf','MemberController@kf');//客服

	});

	Route::group(['prefix'=>'group','middleware' => ['apiauth']], function () {
		Route::any('add','GroupController@add');
		Route::any('getinfo','GroupController@getinfo');
		Route::any('start','GroupController@start');
		Route::any('change','GroupController@change');
		Route::any('invite','GroupController@invite');
		Route::any('inviteme','GroupController@inviteme');
		Route::any('invitelist','GroupController@invitelist');
		Route::any('inviteyes','GroupController@inviteyes');
		Route::any('delmember','GroupController@delmember');
		Route::any('exitmember','GroupController@exitmember');
		Route::any('getall','GroupController@getall');		
	});

	//比赛 
	Route::group(['prefix'=>'games','middleware' => ['apiauth']], function () {
		Route::any('addcollect','GamesController@addcollect');
		Route::any('delcollect','GamesController@delcollect');

		Route::any('getwarningreason','GamesController@getwarningreason');
		Route::any('addwarning','GamesController@addwarning');
	});

	Route::group(['prefix'=>'gamelog'], function () { //无权限
		Route::any('school','GamelogController@school');
		Route::any('getall','GamelogController@getall');
		Route::any('day','GamelogController@day');
	});
	Route::group(['prefix'=>'gamelog','middleware' => ['apiauth']], function () {
		Route::any('chose','GamelogController@chosedateaddress');
		Route::any('chosesave','GamelogController@savechosedateaddress');
		Route::any('saveresult','GamelogController@saveresult');		
		Route::any('start','GamelogController@startmatchlog');
		Route::any('votesave','GamelogController@votesave');
	});

	Route::group(['prefix'=>'gteam'], function () { //无权限
		Route::any('getall','GteamController@getall');
		Route::any('info','GteamController@info');
		Route::any('allmembers','GteamController@allmembers');
	});

	Route::group(['prefix'=>'gteam'], function () {
		Route::any('school','GteamController@school');		
		Route::any('creat','GteamController@creat');
		Route::any('listrelation','GteamController@listrelation');
		Route::any('addmember','GteamController@addmember');
		Route::any('delmember','GteamController@delmember');
		Route::any('quitmember','GteamController@quitmember');
		
		Route::any('changeleader','GteamController@changeleader');
		Route::any('updateicon','GteamController@updateicon');
		Route::any('updatename','GteamController@updatename');
		Route::any('updatemembername','GteamController@updatemembername');
		Route::any('updateisshowmsg','GteamController@updateisshowmsg');
		Route::any('updateisshowname','GteamController@updateisshowname');
		Route::any('updatenumber','GteamController@updatenumber');

		Route::any('invite','GteamController@invite');
		Route::any('invitelist','GteamController@invitelist');
		Route::any('inviteyes','GteamController@inviteyes');		

		Route::any('viewinfo','GteamController@viewinfo');
		Route::any('detail','GteamController@detail');

		Route::any('getallme','GteamController@getallme');
	});
	
});






