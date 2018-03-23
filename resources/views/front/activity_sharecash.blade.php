@extends('front.common.activity_common')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
    <link href="/css/shareCash.css" rel="stylesheet">
@endsection

@section('content')
    <!--分享按钮-->
    <a href="/activity/sharecashinfo"><p class="shareExplant">分享说明</p></a>

    <!--3个信息-->
    <div style="height: 150px">
        <ul class="am-avg-sm-3 boxes">
            <li class="box box-1">
                <p>{{empty($listArr)?0:count($listArr)}}</p>
                <p>名新增用户</p>
            </li>

            <li class="box box-2">
                <p style="color: #E9B11E">{{$total}}元</p>
                <p>累计金额</p>
            </li>

            <li class="box box-3">
                <p style="color: #E9B11E">{{$total-$withdraw}}元</p>
                <p>可提现金额</p>
            </li>
        </ul>
    </div>

    <hr/>

    <!--    底部的cell-->
    @if(!empty($listArr))
        @foreach($listArr as $v)
            <div class="cell">
                <img src="{{$v->icon}}" class="headerImage"/>
                <ul class="mx_ul">
                    <li>
                        <p class="persontitle am-text-top">{{$v->name}}：{{empty($v->statusstr)?'':$v->statusstr}}</p>
                    </li>
                    <li>
                        <div class="am-progress am-progress-xs progress_Mx">
                            <div class="am-progress-bar" style="width: {{empty($v->percent)?0:(int)$v->percent}}%"></div>
                        </div>
                    </li>
                </ul>
                {{empty($v->moneystr)?'':$v->moneystr}}
                <!-- <input class="addFriend" type="button" value="添加好友"> -->
            </div>
        @endforeach
    @endif
    
    @if(empty($company))
        @if($total-$withdraw > 20)
            <a href="javascript:;" dataurl="{{url('activity/withdraw?')}}" class="ajaxurl" ><button class="navbar-fixed-bottom sureBtn">提现</button></a>
        @endif
    @endif
    <a href="javascript:;" dataurl="{{url('activity/withdrawinfo?')}}" class="ajaxurl"><button class="navbar-fixed-bottom sureBtn sureBtnTwo">提现明细</button></a>
    <div style="display: block;height: 150px"></div>


@endsection

@section('scripts')
@include('front.common.activity_login_js')
@endsection
