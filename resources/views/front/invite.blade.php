<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link type="text/css" rel="stylesheet" href="/css/h5_Regist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <title>注册-邀请</title>
</head>
<body>
    <form id="subform" method="POST" action="" accept-charset="UTF-8">
        <input type="hidden" name="recommend" value="{{$recommend}}">
        <div>
            <p class="titleForText">注册龙少足球</p>
            <p class="smallTitle" style="padding-top:50px">手机号</p>
            <input id="phoneString" name="mobile" placeholder="请输入手机号" class="inputForText" type="tel">
            <p class="grayline"></p>
            <p class="smallTitle" style="padding-top: 25px">验证码</p>
            <input id="codeString" placeholder="请输入验证码" class="inputForText" name="code" type="tel">
            <a id="code" class="codeText" style="cursor:pointer;" >获取验证码</a>
            <p class="grayline"></p>
            <p class="productgreement">请认真阅读<a href="/txt/register" target="__blank" > 注册条款</a>，默认确认</p>
        </div>
        <a id="SureForBtn" class="SureBtn ajaxformsubmit"  style="cursor:pointer;">确定并下载</a>
    </form>
</body>
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
@include('front.common.error_common')
<script type="text/javascript">
$(document).ready(function (){
    var time = 60;
    //获取验证码60s倒计时
    $("a.codeText").on("click",function(){
        var thbtn = $(this);
        var mobile = $('#phoneString').val();
        if(!/^(13[0-9]|14[0-9]|15[0-9]|17[0-9]|18[0-9])\d{8}$/i.test(mobile)){
            sendError('请输入正确的手机号');
            return false;
        }
        if(time == 60){
            $.ajax({
                type: "GET",
                url: "/api/v1/sendmessage?type=reg&mobile="+ mobile,
                dataType: "json",
                success: function (da) {
                    sendError(da.msg);
                    if (da.error == 0) {
                        settime(thbtn);
                    }
                },
            });
            
        }
        return false;            
    }) 

    $(".ajaxformsubmit").on("click",function(){    
        $.ajax({
            type: "POST",
            url: '/ajaxsignin',
            data:$(this).parents('form').serialize(),
            dataType: "json",
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            success: function (da) {
                sendError(da.msg);
                if (da.error == 0) {
                    window.location.href = da.url; 
                }
            },
        });
        return false;            
    })


    function settime(obj) { 
        if (time == 0) { 
            obj.attr("disabled", false);    
            obj.text("获取验证码"); 
            time = 60; 
            return;
        } else { 
            obj.attr("disabled", true); 
            obj.text("重新发送(" + time + ")"); 
            time--; 
        } 
        setTimeout(function() { settime(obj) },1000) 
    }   
});
</script> 

</html>