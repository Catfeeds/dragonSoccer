@extends('front.activePage.headerForFooter')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
@endsection

@section('content')
    <!--顶部-->
    <div class="cell">
        <img src="/imgs/boyForImage.png" class="headerImage"/>
        <ul class="mx_ul">
            <li>
                <p class="persontitle am-text-top">刘小虎：1000票</p>
            </li>
            <li>
                <div class="am-progress am-progress-xs progress_Mx">
                    <div class="am-progress-bar" style="width: 80%"></div>
                </div>
            </li>
        </ul>
        <input class="upforbutton" style="color:#F35D5D" type="button" value="举报">
    </div>
    <!--富文本编辑-->
    <hr/>

    <a><button class="navbar-fixed-bottom sureBtn">投票</button></a>

@endsection

