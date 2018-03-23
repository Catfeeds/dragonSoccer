@extends('front.common.activity_common')

@section('css')
    <link href="/css/withdrawalsCss.css" rel="stylesheet">
    <style type="text/css">
        .danxuan
        {
            display: inline-block;
            float: right;
            margin-top: 20px!important;
            margin-right: 20px!important;
        }

    </style>
@endsection

@section('content')

    <!-- <div class="tableView ajaxpayway">
        <p class="cell  active" dataval='ali'><img src="/imgs/zhifubao.png">支付宝</p>
        <hr/>
        <p class="cell" dataval='wechat'><img src="/imgs/WeChat.png">微信</p>
    </div> -->

    <div class="tableView ">
        <p class="cell " dataval='ali'><img src="/imgs/zhifubao.png">支付宝<input name="Fruit"  type="radio" value="ali" class="danxuan ajaxpayway" /></p>
        <hr/>
        <p class="cell" dataval='wechat'><img src="/imgs/WeChat.png">微信<input name="Fruit"   type="radio" value="wechat" class="danxuan ajaxpayway" /></p>
    </div>


    <p class="smalltext firstForPlayout">请授先权支付宝或微信即可提现</p>
    <p class="bigtext">操作步骤：</p>
    <p class="smalltext">我的-个人资料-第三方授权</p>

    @if(empty($company))
        @if($total > 20)
            <div class="navbar-fixed-bottom buttomDiv" >
                <p>可提现:<p class="money">￥{{$total}}</p></p>
                <a><p class="suerbutton ajaxformsubmit">提现</p></a>
            </div>
        @endif
    @endif

@endsection

@section('scripts')
@include('front.common.activity_login_js')
<script type="text/javascript">
$(document).ready(function (){
    $(".ajaxformsubmit").on("click",function(){  
        var payway = $('.ajaxpayway:checked').val();  
        $.ajax({
            type: "POST",
            url: '{{url("activity/ajaxwithdraw?")}}'+parameter,
            data:{'payway':payway},
            dataType: "json",
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            success: function (da) {
                sendError(da.msg);
                if (da.error == 0) {
                    window.location.href = "{{url('activity/withdrawinfo?')}}"+parameter; 
                }
            },
        });
        return false;            
    })  
})  
</script>

@endsection
