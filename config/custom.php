<?php
//全站配置文件，主要用于显示 数据库中值和view层面的显示对应 
//eg: statusArr = ['1'=>'待支付']
/*  格式
    控制器/表 =>[ 
            状态 =>[
                1=>'待支付'
            ]
        ]
*/
return [
    'match' => [
        'statusArr' => ['n'=>'未发布','y'=>'已发布'],
        'teamstsArr' => ['w'=>'匹配中','s'=>'自动匹配完成','f'=>'开启自动失败','o'=>'匹配完成','c1'=>'城市淘汰赛第1轮','c2'=>'城市淘汰赛第2轮','c3'=>'城市淘汰赛第3轮','p1'=>'省级淘汰赛第1轮','p2'=>'省级淘汰赛第2轮','p3'=>'省级淘汰赛第3轮','p4'=>'省级淘汰赛第4轮','t1'=>'全国赛'], //1108
        'matchlevelArr' => ['o','c1','c2','c3','p1','p2','p3','p4','t1'], //1108

        'levelArr'  => ['child'=>'13-15周岁','boy'=>'16-18周岁','young'=>'19-22周岁'],
        'levelnumArr'  => ['child'=>['min'=>13,'max'=>15],'boy'=>['min'=>16,'max'=>18],'young'=>['min'=>19,'max'=>23] ],
        'ruleArr'   => ['small'=>'7人制'],
        'rulenumArr'=> ['small'=>'9'],
        'rulePositionArr'=> [
            'small'=>['f'=>'3','m'=>'2','b'=>'2','gk'=>'2'],
        ],

        'sexArr'    => ['f'=>'男','m'=>'女','fm'=>'男女混合'],
        'shareurl'  => 'http://lszq.dragonfb.com/downloapak'
        
    ],

    'notice' => [
        'statusArr' => ['n'=>'未发布','y'=>'已发布'],
    ],

    'member' => [
        'positionArr' => ['f'=>'前锋','m'=>'中场','b'=>'后卫','gk'=>'守门员'],
        'footArr' => ['l'=>'左脚','r'=>'右脚'],
        'sexArr'    => ['f'=>'男','m'=>'女'],
        'statusArr' => ['n'=>'未认证','w'=>'认证中','y'=>'已认证','p'=>'证件照模糊','f'=>'填写信息与上传证件照不符'],
        'statusArr2' => ['y'=>'通过认证','p'=>'证件照模糊','f'=>'填写信息与上传证件照不符'],
        'easemobArr' => ['member'=>'m','group'=>'g'], //环信前缀
        'easemobtypeArr' => ['member'=>'users','group'=>'chatgroups'], //环信消息类型

    ],

    'matchwarning' => [
        'reasonArr' => ['fake'=>'发布伪冒品信息','indecent'=>'淫秽内容','cheat'=>'信息与现状不符','breaklaw'=>'违法信息'],
    ],

    'apply' => [
        'statusArr' => ['1'=>'报名成功','2'=>'已结束','3'=>'失败','4'=>'解散','5'=>'完成组队','6'=>'匹配中','7'=>'匹配中','8'=>'匹配成功'],

        'statusmsgArr' => ['1'=>'报名成功','2'=>'已结束','3'=>'人数不够，失败','4'=>'解散','5'=>'完成组队','6'=>'匹配中，请耐心等待','7'=>'匹配中，请耐心等待','8'=>'进群'],
    ],

    'applyinvite' => [
        'statusArr' => ['1'=>'等待同意','2'=>'拒绝','3'=>'失效','4'=>'同意'],
        'groupnumber'=>12,
    ],

    'team' => [
        'teamnameArr' => ['f'=>'龙之少年','m'=>'龙少足球'],
        'stsArr' => ['w'=>'待定','s'=>'晋级','f'=>'淘汰'],//1108
    ],


    'system' => [
        'easemobArr' => ['addfriend'=>'notification','addgroup'=>'notification','delgroup'=>'notification','changegroupleader'=>'notification','invitefriend'=>'notification','inviteacceptfriend'=>'notification','login'=>'notification'],
        'easemobmsgArr' => ['addfriend'=>'您有好友申请','addgroup'=>'您有好友加您进群','delgroup'=>'您被移除群','changegroupleader'=>'您已成为群主，名称：','invitefriend'=>'您有好友邀请您参加：','inviteacceptfriend'=>'您有好友同意您的邀请，赛事名称：','login'=>date('y-m-d H:i:s').'登录了龙少足球'],
        'easemobtypeArr' => ['member'=>'users','group'=>'chatgroups'], //环信消息类型

        'freezingtime'=>'3600',//冻结时间
        'freezingtimeexponent'=>'2',//冻结时间指数  例如：指数3，第一次冻结1 ，第二次冻结3，第三次冻结9

    ],

    'memberwarning' => [
        'reasonArr' => ['fake'=>'此账号可能被盗用了','indecent'=>'发布不适当内容对我造成骚扰','cheat'=>'存在欺诈骗钱行为','breaklaw'=>'存在侵权行为'],
    ],

    'cash' => [ 
        'typeArr' => ['apply'=>'注册活动','donation'=>'捐款','platform'=>'平台'], 
        'admintypeArr' => ['donation'=>'捐款','platform'=>'平台'], 
        'moneyArr' => ['joinmatch'=>'3','quitmatch'=>'3'],//开始匹配 奖金池加3 ，取消匹配奖金池减3， 匹配失败减3

    ],

    'matchlog' => [
        'statusArr' => ['mw'=>'待定','mc'=>'待审核','mwate'=>'准备','mready'=>'即将开始','mgo'=>'开始','end'=>'比赛结束','eupc'=>'结果审核','eover'=>'结束'],//1108
        'statusmsgArr' =>['m'=>'即将开始','e'=>'已结束']
    ],

    'order' => [
        'statusArr' => ['1'=>'取消订单','2'=>'待支付','3'=>'成功','4'=>'失败'],
        'paywayArr' => ['apple'=>'苹果内购','ali'=>'支付宝','wechat'=>'微信'],
    ],

    'balancelog' => [
        'typeArr' => ['mall'=>'商城购买','login'=>'签到成功','vote'=>'投票'],
        'symbolArr'=> ['mall'=>'+','login'=>'+','vote'=>'-'],
        'ratio' => 10, //一块钱兑换10个珠子
    ],

    'activityArr' => [
        'statusArr' => ['w'=>'待审核','n'=>'失败','y'=>'成功'],        
        'regcash'=>['title'=>'分享 APP  赢现金好礼','url'=> PHP_SAPI==='cli'?false:url('activity/sharecash?'),'time'=>'火爆进行中','img'=>'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/aindex001.jpg'],
        'spokesman'=>['title'=>'2018龙之星（暨形象代言人）选拔活动','url'=> PHP_SAPI==='cli'?false:url('activity/spokesman/list?'),'time'=>'2018年1月1日--2018年5月1日','img'=>'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/aindex002.jpg'],
    ],

    'money' => [ 
        'typeArr' => ['apply6'=>'已报名','apply8'=>'参加比赛','withdraw'=>'提现'], 
        'moneyArr' => ['apply6'=>'2','apply8'=>'4'],

    ],

    'orderwithdraw' => [ 
        'statusArr' => [0=>'失败',1=>'待审核',2=>'支付中', 3=>'成功', 4=>'失败',5=>'结束'], 

    ],

    //v2.0
    'gamewarning' => [
        'reasonArr' => ['fake'=>'此账号可能被盗用了','indecent'=>'发布不适当内容对我造成骚扰','cheat'=>'存在欺诈骗钱行为','breaklaw'=>'存在侵权行为'],
    ],
    
    
];