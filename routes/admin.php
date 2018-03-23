<?php
Route::get('login','LoginController@login');
Route::post('ajaxlogin', 'LoginController@ajaxlogin');



Route::group(['middleware' => ['adminauth','adminsidebar']], function () {
	Route::get('/','HomeController@index');
	Route::get('logout', 'LoginController@logout');
});

Route::group(['middleware' => ['adminauth','adminsidebar','adminpermission']], function () {	
	//权限管理-------------------------------------
	//权限
	Route::get('permission/index/{id?}','PermissionController@index')->name('admin.permission.index'); 
	Route::get('permission/list','PermissionController@lists')->name('admin.permission.list'); //二级权限列表
	Route::get('permission/add','PermissionController@add')->name('admin.permission.add'); 
	Route::get('permission/ajaxgetcon','PermissionController@ajaxgetcon')->name('admin.permission.ajaxgetcon'); //获取二级权限
	Route::post('permission/ajaxadd','PermissionController@ajaxadd')->name('admin.permission.ajaxadd'); 
	Route::get('permission/edit','PermissionController@edit')->name('admin.permission.edit'); 
	Route::post('permission/ajaxedit','PermissionController@ajaxedit')->name('admin.permission.ajaxedit'); 
	Route::get('permission/ajaxdel','PermissionController@ajaxdel')->name('admin.permission.ajaxdel'); 

	//角色管理
	Route::get('role/index','RoleController@index')->name('admin.role.index'); 
	Route::get('role/add','RoleController@add')->name('admin.role.add'); 
	Route::post('role/ajaxadd','RoleController@ajaxadd')->name('admin.role.ajaxadd'); 
	Route::get('role/edit','RoleController@edit')->name('admin.role.edit'); 
	Route::post('role/ajaxedit','RoleController@ajaxedit')->name('admin.role.ajaxedit'); 
	Route::get('role/ajaxdel','RoleController@ajaxdel')->name('admin.role.ajaxdel'); 

	//用户管理
	Route::get('adminuser/index','AdminuserController@index')->name('admin.adminuser.index'); 
	Route::get('adminuser/add','AdminuserController@add')->name('admin.adminuser.add'); 
	Route::post('adminuser/ajaxadd','AdminuserController@ajaxadd')->name('admin.adminuser.ajaxadd'); 
	Route::get('adminuser/edit','AdminuserController@edit')->name('admin.adminuser.edit'); 
	Route::post('adminuser/ajaxedit','AdminuserController@ajaxedit')->name('admin.adminuser.ajaxedit'); 
	Route::get('adminuser/ajaxdel','AdminuserController@ajaxdel')->name('admin.adminuser.ajaxdel');

	//赛事管理
	Route::get('match/index','MatchController@index')->name('admin.match.index'); 
	Route::get('match/add','MatchController@add')->name('admin.match.add'); 
	Route::post('match/ajaxadd','MatchController@ajaxadd')->name('admin.match.ajaxadd'); 
	Route::get('match/edit','MatchController@edit')->name('admin.match.edit'); 
	Route::post('match/ajaxedit','MatchController@ajaxedit')->name('admin.match.ajaxedit'); 
	Route::get('match/ajaxdel','MatchController@ajaxdel')->name('admin.match.ajaxdel');

	//公告管理
	Route::get('notice/index','NoticeController@index')->name('admin.notice.index'); 
	Route::get('notice/add','NoticeController@add')->name('admin.notice.add'); 
	Route::post('notice/ajaxadd','NoticeController@ajaxadd')->name('admin.notice.ajaxadd'); 
	Route::get('notice/edit','NoticeController@edit')->name('admin.notice.edit'); 
	Route::post('notice/ajaxedit','NoticeController@ajaxedit')->name('admin.notice.ajaxedit'); 
	Route::get('notice/ajaxdel','NoticeController@ajaxdel')->name('admin.notice.ajaxdel');
	Route::post('notice/ajaxupdate','NoticeController@ajaxupdate')->name('admin.notice.ajaxupdate'); 

	//会员管理
	Route::get('member/index','MemberController@index')->name('admin.member.index'); 
	Route::get('member/add','MemberController@add')->name('admin.member.add'); 
	Route::post('member/ajaxadd','MemberController@ajaxadd')->name('admin.member.ajaxadd'); 
	Route::get('member/edit','MemberController@edit')->name('admin.member.edit'); 
	Route::post('member/ajaxedit','MemberController@ajaxedit')->name('admin.member.ajaxedit'); 
	Route::get('member/ajaxdel','MemberController@ajaxdel')->name('admin.member.ajaxdel');
	Route::get('member/ajaxreset','MemberController@ajaxreset')->name('admin.member.ajaxreset');
	
	//公司管理
	Route::get('company/index','CompanyController@index')->name('admin.company.index'); 
	Route::get('company/view','CompanyController@view')->name('admin.company.view'); 
	Route::get('company/export','CompanyController@export')->name('admin.company.export'); 
	Route::get('company/add','CompanyController@add')->name('admin.company.add'); 
	Route::post('company/ajaxadd','CompanyController@ajaxadd')->name('admin.company.ajaxadd'); 
	Route::get('company/edit','CompanyController@edit')->name('admin.company.edit'); 
	Route::post('company/ajaxedit','CompanyController@ajaxedit')->name('admin.company.ajaxedit'); 
	

	//群管理
	Route::get('team/index','TeamController@index')->name('admin.team.index'); 
	Route::get('team/alist','TeamController@alist')->name('admin.team.alist'); 
	Route::get('team/add','TeamController@add')->name('admin.team.add'); 
	Route::post('team/ajaxadd','TeamController@ajaxadd')->name('admin.team.ajaxadd'); 
	Route::get('team/edit','TeamController@edit')->name('admin.team.edit'); 
	Route::post('team/ajaxedit','TeamController@ajaxedit')->name('admin.team.ajaxedit'); 
	Route::get('team/ajaxdel','TeamController@ajaxdel')->name('admin.team.ajaxdel'); 
	Route::get('team/ajaxgetmember','TeamController@ajaxgetmember')->name('admin.team.ajaxgetmember'); 


	//banner管理
	Route::get('banner/index','BannerController@index')->name('admin.banner.index'); 
	Route::get('banner/add','BannerController@add')->name('admin.banner.add'); 
	Route::post('banner/ajaxadd','BannerController@ajaxadd')->name('admin.banner.ajaxadd'); 
	Route::get('banner/edit','BannerController@edit')->name('admin.banner.edit'); 
	Route::post('banner/ajaxedit','BannerController@ajaxedit')->name('admin.banner.ajaxedit'); 
	Route::get('banner/ajaxdel','BannerController@ajaxdel')->name('admin.banner.ajaxdel');

	//奖金池管理
	Route::get('cash/index','CashController@index')->name('admin.cash.index'); 
	Route::get('cash/add','CashController@add')->name('admin.cash.add'); 
	Route::post('cash/ajaxadd','CashController@ajaxadd')->name('admin.cash.ajaxadd'); 
	Route::get('cash/edit','CashController@edit')->name('admin.cash.edit'); 
	Route::post('cash/ajaxedit','CashController@ajaxedit')->name('admin.cash.ajaxedit'); 
	Route::get('cash/ajaxdel','CashController@ajaxdel')->name('admin.cash.ajaxdel');

	//报名
	Route::get('apply/index','ApplyController@index')->name('admin.apply.index'); 


	//赛程管理
	Route::get('matchlog/index','MatchlogController@index')->name('admin.matchlog.index'); 
	Route::get('matchlog/view','MatchlogController@view')->name('admin.matchlog.view'); 
	Route::post('matchlog/ajaxsave','MatchlogController@ajaxsave')->name('admin.matchlog.ajaxsave'); 

	//商品管理
	Route::get('goods/index','GoodsController@index')->name('admin.goods.index'); 
	Route::get('goods/add','GoodsController@add')->name('admin.goods.add'); 
	Route::post('goods/ajaxadd','GoodsController@ajaxadd')->name('admin.goods.ajaxadd'); 
	Route::get('goods/edit','GoodsController@edit')->name('admin.goods.edit'); 
	Route::post('goods/ajaxedit','GoodsController@ajaxedit')->name('admin.goods.ajaxedit'); 
	Route::get('goods/ajaxdel','GoodsController@ajaxdel')->name('admin.goods.ajaxdel');

	//订单管理
	Route::get('orders/index','OrdersController@index')->name('admin.orders.index'); 
	Route::get('orders/view','OrdersController@view')->name('admin.orders.view'); 

	//代言人管理
	Route::get('activityapply/index','ActivityapplyController@index')->name('admin.activityapply.index'); 
	Route::get('activityapply/vote','ActivityapplyController@vote')->name('admin.activityapply.vote');//投票 
	Route::get('activityapply/add','ActivityapplyController@add')->name('admin.activityapply.add'); 
	Route::post('activityapply/ajaxadd','ActivityapplyController@ajaxadd')->name('admin.activityapply.ajaxadd'); 
	Route::get('activityapply/edit','ActivityapplyController@edit')->name('admin.activityapply.edit'); 
	Route::post('activityapply/ajaxedit','ActivityapplyController@ajaxedit')->name('admin.activityapply.ajaxedit');

	
	Route::get('member/money','MemberController@money')->name('admin.member.money'); //分享奖金
	Route::get('member/withdraw','MemberController@withdraw')->name('admin.member.withdraw'); //提现记录
	Route::get('member/withdrawview','MemberController@withdrawview')->name('admin.member.withdrawview'); //提现记录
	Route::any('member/ajaxwithdraw','MemberController@ajaxwithdraw')->name('admin.member.ajaxwithdraw'); //提现记录


	//赛事管理 2.0
	Route::get('games/index','GamesController@index')->name('admin.games.index'); 
	Route::get('games/add','GamesController@add')->name('admin.games.add'); 
	Route::get('games/edit','GamesController@edit')->name('admin.games.edit'); 
	Route::get('games/ajaxages','GamesController@ajaxages')->name('admin.games.ajaxages'); 
	Route::get('games/ajaxruler','GamesController@ajaxruler')->name('admin.games.ajaxruler'); 
	Route::get('games/ajaxrulerinfo','GamesController@ajaxrulerinfo')->name('admin.games.ajaxrulerinfo'); 
	Route::post('games/ajaxadd','GamesController@ajaxadd')->name('admin.games.ajaxadd'); 
	Route::post('games/ajaxedit','GamesController@ajaxedit')->name('admin.games.ajaxedit');

	Route::get('games/content','GamesController@content')->name('admin.games.content'); 
	Route::get('games/addcontent','GamesController@addcontent')->name('admin.games.addcontent'); 
	Route::get('games/editcontent','GamesController@editcontent')->name('admin.games.editcontent'); 
	Route::post('games/ajaxaddcontent','GamesController@ajaxaddcontent')->name('admin.games.ajaxaddcontent'); 
	Route::post('games/ajaxeditcontent','GamesController@ajaxeditcontent')->name('admin.games.ajaxeditcontent');

	//主办方管理 2.0
	Route::get('school/index','SchoolController@index')->name('admin.school.index'); 
	Route::get('school/add','SchoolController@add')->name('admin.school.add'); 	
	Route::get('school/edit','SchoolController@edit')->name('admin.school.edit'); 
	Route::post('school/ajaxadd','SchoolController@ajaxadd')->name('admin.school.ajaxadd'); 
	Route::post('school/ajaxedit','SchoolController@ajaxedit')->name('admin.school.ajaxedit'); 


	//网站配置 2.0
	Route::get('webconfig/index','WebconfigController@index')->name('admin.webconfig.index'); 
	Route::get('webconfig/ajaxsaveval','WebconfigController@ajaxsaveval')->name('admin.webconfig.ajaxsaveval'); 

	//报名管理 2.0
	Route::get('group/index','GroupController@index')->name('admin.group.index'); 
	Route::get('group/add','GroupController@add')->name('admin.group.add'); 
	Route::get('group/edit','GroupController@edit')->name('admin.group.edit'); 
	Route::post('group/ajaxadd','GroupController@ajaxadd')->name('admin.group.ajaxadd'); 
	Route::post('group/ajaxedit','GroupController@ajaxedit')->name('admin.group.ajaxedit'); 
	Route::get('group/ajaxages','GroupController@ajaxages')->name('admin.group.ajaxages'); 
	Route::get('group/ajaxmember','GroupController@ajaxmember')->name('admin.group.ajaxmember'); 
	Route::get('group/ajaxdelmember','GroupController@ajaxdelmember')->name('admin.group.ajaxdelmember'); 
	Route::post('group/ajaxaddmember','GroupController@ajaxaddmember')->name('admin.group.ajaxaddmember');
	Route::get('group/ajaxdel','GroupController@ajaxdel')->name('admin.group.ajaxdel');
	


	//队伍管理 2.0
	Route::get('gteam/index','GteamController@index')->name('admin.gteam.index'); 
	Route::get('gteam/add','GteamController@add')->name('admin.gteam.add'); 
	Route::get('gteam/edit','GteamController@edit')->name('admin.gteam.edit'); 
	Route::post('gteam/ajaxadd','GteamController@ajaxadd')->name('admin.gteam.ajaxadd'); 
	Route::post('gteam/ajaxedit','GteamController@ajaxedit')->name('admin.gteam.ajaxedit'); 
	Route::get('gteam/ajaxages','GteamController@ajaxages')->name('admin.gteam.ajaxages');
	Route::get('gteam/ajaxgroup','GteamController@ajaxgroup')->name('admin.gteam.ajaxgroup');	
	Route::get('gteam/ajaxdelmember','GteamController@ajaxdelmember')->name('admin.gteam.ajaxdelmember');
	Route::get('gteam/ajaxdel','GteamController@ajaxdel')->name('admin.gteam.ajaxdel');


	//赛程 2.0
	Route::get('gamelog/index','GamelogController@index')->name('admin.gamelog.index'); 
	Route::get('gamelog/add','GamelogController@add')->name('admin.gamelog.add'); 
	Route::get('gamelog/edit','GamelogController@edit')->name('admin.gamelog.edit'); 
	Route::post('gamelog/ajaxadd','GamelogController@ajaxadd')->name('admin.gamelog.ajaxadd'); 
	Route::post('gamelog/ajaxedit','GamelogController@ajaxedit')->name('admin.gamelog.ajaxedit'); 
	Route::get('gamelog/ajaxages','GamelogController@ajaxages')->name('admin.gamelog.ajaxages');
	Route::get('gamelog/ajaxteam','GamelogController@ajaxteam')->name('admin.gamelog.ajaxteam');
	Route::get('gamelog/ajaxteamruler','GamelogController@ajaxteamruler')->name('admin.gamelog.ajaxteamruler');
		   
});

  

