
@extends('front.activePage.headerForFooter')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
    <link href="/css/shareCash.css" rel="stylesheet">
@endsection

@section('content')
    <!--分享按钮-->
    <a href="explain"><p class="shareExplant">分享说明</p></a>

    <!--3个信息-->
    <div style="height: 150px">
        <ul class="am-avg-sm-3 boxes">
            <li class="box box-1">
                <p>2</p>
                <p>名新增用户</p>
            </li>

            <li class="box box-2">
                <p style="color: #E9B11E">22元</p>
                <p>累计金额</p>
            </li>

            <li class="box box-3">
                <p style="color: #E9B11E">2.22元</p>
                <p>已赞现金</p>
            </li>
        </ul>
    </div>

    <hr/>

    <!--    底部的cell-->
    <div class="cell">
        <img src="/imgs/boyForImage.png" class="headerImage"/>
        <ul class="mx_ul">
            <li>
                <p class="persontitle am-text-top">刘小虎：已注册</p>
            </li>
            <li>
                <div class="am-progress am-progress-xs progress_Mx">
                    <div class="am-progress-bar" style="width: 80%"></div>
                </div>
            </li>
        </ul>
        <input class="addFriend" type="button" value="添加好友">
    </div>


    <button class="navbar-fixed-bottom sureBtn">分享</button>
@endsection
