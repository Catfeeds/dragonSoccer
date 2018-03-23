@extends('front.activePage.headerForFooter')

@section('css')
    <link href="/css/withdrawalsCss.css" rel="stylesheet">
@endsection

@section('content')

    <div class="tableView">
        <p class="cell"><img src="/imgs/zhifubao.png">支付宝</p>
        <hr/>
        <p class="cell"><img src="/imgs/WeChat.png">微信</p>
    </div>

    <p class="smalltext firstForPlayout">请授先权支付宝或微信即可提现</p>
    <p class="bigtext">操作步骤：</p>
    <p class="smalltext">我的-个人资料-第三方授权</p>

    <div class="navbar-fixed-bottom buttomDiv" >
        <p>可提现:<p class="money">￥20.00</p></p>
        <a><p class="suerbutton">提现</p></a>
    </div>

@endsection
