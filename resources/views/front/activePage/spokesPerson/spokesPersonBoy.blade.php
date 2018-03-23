
@extends('front.activePage.headerForFooter')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
@endsection

@section('content')

    <!--标题-->
    <p class="title">2018形象代言人(男)</p>
    <p class="context">201392人参与</p>
    <p class="timeText">2017年10月22日-2018年4月1</p>
    <hr/>
    <!--标题end-->

    <!--参赛球员-->
    <p class="title">参赛球员</p>


    <div class="cell">
        <a href="spokesPersonDetail"><img src="/imgs/boyForImage.png" class="headerImage"/></a>
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
        <input class="upforbutton" type="button" value="支持">
    </div>

    <a href="spokesPersonExplain"><button class="navbar-fixed-bottom sureBtn">参加活动</button></a>

@endsection


