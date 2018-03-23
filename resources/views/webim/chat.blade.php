@extends('webim.common')
@section('css')
<link rel="stylesheet" type="text/css" href="/webim/webim.css">
<style type="text/css">
.active{
    background-color:hsla(0,0%,100%,.1);
}   
</style>
@endsection
@section('content')
    <div class="row" style="height:60px; line-height: 60px;">
        <div class="col-xs-12" style="background-color:#eee; border-bottom:1px solid #ddd;">
            <img style="margin-right:5px;" class="img-circle" src="{{empty($icon)?'http://lzsn-icon.oss-cn-beijing.aliyuncs.com/public/cash_baomibg.png':$icon}}" width="40" height="40">            
            <span class="name">{{$uname}}</span>
            <span class="pull-right ajaxexit">退出</span>
        </div>
    </div>
    <div class="row" style="height:100%;height: 600px;">
        <div class="col-xs-3" style="color:#f4f4f4; background-color:#2e3238; height:90%; ">
            <div style="margin-top: 15px;">
                <input class="ajaxsearch" placeholder="search mobile..."  style="padding:0 10px;width:100%;font-size:9pt;color:#fff;height:30px;line-height:30px;border:1px solid #3a3a3a;border-radius:4px;outline:0;background-color:#26292e;">
            </div>
            <div style="">
                <ul class="clearfix list-unstyled ajaxtomember" style="margin-top:20px;  min-height:300px;">
                    @if(!empty($mArr))
                        @foreach($mArr as $k=>$v)
                            <li class="pull-left @if($k==0) active @endif" style="height: 28px; line-height: 28px; margin-bottom:5px; width:95%; " datename="m{{$v['id']}}" >
                                <img style="margin-right:5px;" src="{{$v['icon']}}" width="28" class="img-circle pull-left">
                                <span class="pull-left hidden-xs" >{{$v['name']}}-{{$v['mobile']}}</span>
                                <span class="pull-left number" style="color:red; font-size: 28px; margin-left:5px;"></span>
                            </li>
                        @endforeach
                    @endif
                </ul>        
            </div>
        </div>
        <div class="col-xs-9" style="overflow:hidden;background-color:#eee; height:90%; padding-left:0px; padding-right: 0px;">
            <div id="chatmsg" style="height:calc(100% - 10pc); padding:10px 0px; overflow-y:scroll;overflow-x:hidden;word-break:break-all;">
                <ul class="clearfix list-unstyled ajaxchatmsg">
                    
                </ul>
            </div>
            <div style="height:10pc;border-top:1px solid #ddd;">
                <textarea class="ajaxcontent" style="height:75%;width:100%;border:none;outline:0;font-family:Micrsofot Yahei;resize:none;padding:0px 15px;"></textarea>
                <input class="pull-right ajaxsend" type="button" name="" value="发送" style="margin-top: 5px; margin-right:15px; ">      
            </div>    
        </div>
    </div>
@endsection
@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    //左侧搜索
    $(document).on('blur','.ajaxsearch',function(){ 
        var mobile = $(this).val();  
        $.ajax({
            type: "get",
            url: "/webim/ajaxgetmemberbymobile",
            data:{'mobile':mobile},
            dataType: "json",
            success: function (da) {
                $flag = true;
                $('.ajaxtomember li').each(function(){
                    var datename = $(this).attr('datename');
                    if(datename == da.mid){
                        $flag = false;
                        $('.ajaxtomember li').removeClass('active');
                        $(this).addClass('active');
                    }
                })
                if($flag){
                    $('.ajaxtomember li').removeClass('active');
                    $('.ajaxtomember').prepend(da.str);
                }                
                getallmsg(); 
            },
        });       
    });

    //左侧选择
    $(document).on('click','.ajaxtomember li',function(){ 
        $('.ajaxtomember li').removeClass('active');
        $(this).addClass('active');
        $(this).find('.number').html('');
        getallmsg();    
    });

    var conn = new WebIM.connection({
        isMultiLoginSessions: WebIM.config.isMultiLoginSessions,
        https: typeof WebIM.config.https === 'boolean' ? WebIM.config.https : location.protocol === 'https:',
        url: WebIM.config.xmppURL,
        heartBeatWait: WebIM.config.heartBeatWait,
        autoReconnectNumMax: WebIM.config.autoReconnectNumMax,
        autoReconnectInterval: WebIM.config.autoReconnectInterval,
        apiUrl: WebIM.config.apiURL,
        isAutoLogin: true
    });
    conn.listen({
        onOpened: function ( message ) {          //连接成功回调
            // 如果isAutoLogin设置为false，那么必须手动设置上线，否则无法收消息
            // 手动上线指的是调用conn.setPresence(); 如果conn初始化时已将isAutoLogin设置为true
            // 则无需调用conn.setPresence();             
        },  
        onClosed: function ( message ) {},         //连接关闭回调
        onTextMessage: function ( message ) {
            messagereturn(message,'txt');
        },    //收到文本消息
        onEmojiMessage: function ( message ) {},   //收到表情消息
        onPictureMessage: function ( message ) {
            console.log(message.url);
            messagereturn(message,'img');
        }, //收到图片消息
        onCmdMessage: function ( message ) {},     //收到命令消息
        onAudioMessage: function ( message ) {},   //收到音频消息
        onLocationMessage: function ( message ) {},//收到位置消息
        onFileMessage: function ( message ) {},    //收到文件消息
        onVideoMessage: function (message) { },   //收到视频消息
        onPresence: function ( message ) {},       //处理“广播”或“发布-订阅”消息，如联系人订阅请求、处理群组、聊天室被踢解散等消息
        onRoster: function ( message ) {},         //处理好友申请
        onInviteMessage: function ( message ) {},  //处理群组邀请
        onOnline: function () {},                  //本机网络连接成功
        onOffline: function () {},                 //本机网络掉线
        onError: function ( message ) {},          //失败回调
        onBlacklistUpdate: function (list) {       //黑名单变动
            // 查询黑名单，将好友拉黑，将好友从黑名单移除都会回调这个函数，list则是黑名单现有的所有好友信息
            //console.log(list);
        },
        onReceivedMessage: function(message){},    //收到消息送达服务器回执
        onDeliveredMessage: function(message){},   //收到消息送达客户端回执
        onReadMessage: function(message){},        //收到消息已读回执
        onCreateGroup: function(message){},        //创建群组成功回执（需调用createGroupNew）
        onMutedMessage: function(message){}        //如果用户在A群组被禁言，在A群发消息会走这个回调并且消息不会传递给群其它成员
    }); 
    //登陆
    var options = { 
        apiUrl: WebIM.config.apiURL,
        user: '{{$uname}}',
        pwd: '{{$pwd}}',
        appKey: WebIM.config.appkey
    };
    conn.open(options); 


    // 单聊发送文本消息
    $(document).on('click','.ajaxsend',function(){ 
        var chat = $('.ajaxtomember .active');                 
        var toname = chat.attr('datename');                
        var content = $('.ajaxcontent').val();
        var chatid = conn.getUniqueId();// 生成本地消息id
        var from = '{{$uname}}';
        $.ajax({
            type: "get",
            url: "/webim/ajaxsetownerchat",
            data:{'to':toname,'content':content},
            dataType: "json",
            success: function (da) {
                if (da.error == 0) {
                    var msg = new WebIM.message('txt',chatid); // 创建文本消息
                    msg.set({
                        msg:content, // 消息内容
                        to: toname, // 接收消息对象（用户id）
                        roomType: false,
                        success: function (id, serverMsgId) {
                            //alert('send success');
                            $('.ajaxcontent').val('');
                            console.log('send private text Success');
                        },
                        fail: function(e){
                            //alert('send fail');
                            console.log("Send private text error");
                        }
                    });
                    msg.body.chatType = 'singleChat';
                    conn.send(msg.body);
                    getallmsg()
                }else{
                    alert(da.msg);
                }
            },
        });
        return false;
    });

    // 单聊贴图发送
    document.addEventListener('paste', function (e) {
        if (e.clipboardData && e.clipboardData.types) {
            if (e.clipboardData.items.length > 0) {
                if (/^image\/\w+$/.test(e.clipboardData.items[0].type)) {
                    var blob = e.clipboardData.items[0].getAsFile();
                    var url = window.URL.createObjectURL(blob);
                    var id = conn.getUniqueId();             // 生成本地消息id
                    var msg = new WebIM.message('img', id);  // 创建图片消息

                    var chat = $('.ajaxtomember .active'); 
                    var toname = chat.attr('datename');               
                    var from = '{{$uname}}';
                    $.ajax({
                        type: "get",
                        url: "/webim/ajaxsetownerchat",
                        data:{'to':toname,'content':url,'type':'img'},
                        dataType: "json",
                        success: function (da) {
                            if (da.error == 0) {
                                msg.set({
                                    apiUrl: WebIM.config.apiURL,
                                    file: {data: blob, url: url},
                                    to: toname,                      // 接收消息对象
                                    roomType: false,
                                    chatType: 'singleChat',
                                    onFileUploadError: function (error) {
                                        console.log('Error');
                                    },
                                    onFileUploadComplete: function (data) {
                                        console.log('Complete');
                                    },
                                    success: function (id) {
                                        console.log('Success');
                                    }
                                });
                                conn.send(msg.body);
                                getallmsg(); 
                            }else{
                                alert(da.msg);
                            }
                        },
                    });
                    return false;  

                }
            }
        }
    });
    
    function getallmsg(){
        var chat = $('.ajaxtomember .active');                 
        var from = chat.attr('datename'); 
        $.ajax({
            type: "get",
            url: "/webim/ajaxgetchat",
            data:{'from':from},
            dataType: "html",
            success: function (da) {
                $('.ajaxchatmsg').html(da); 
                //$('#chatmsg').scrollTop( $('#chatmsg')[0].scrollHeight );   
                document.getelementbyid('#chatmsg').scrollTop( $('#chatmsg')[0].scrollHeight );  
            },
        });
    }

    function messagereturn(message,type){
        //保存消息
        //if(message.from != '{{$uname}}'){
            content = type=='txt'?message.data:message.url;
            $.ajax({
                type: "get",
                url: "/webim/ajaxsetchat",
                data:{'from':message.from,'content':content,'type':type},
                dataType: "json",
                success: function (da) {
                    if (da.error == 0) {                    
                        //getallmsg()
                    }else{
                        alert(da.msg);
                    }
                    console.log(da.msg);
                },
            });
        //}        

        var chatname = $('.ajaxtomember .active').attr('datename'); 
        if(chatname == message.from){ //当前会话
            $(this).find('.number').html('');
            getallmsg();
        }else{
            $flag = true;
            $('.ajaxtomember li').each(function(){
                var datename = $(this).attr('datename');
                if(datename == message.from){
                    $flag = false;
                    $(this).find('.number').html('*');
                }
            })
            if($flag){
                $.ajax({
                    type: "get",
                    url: "/webim/ajaxgetmember",
                    data:{'to':message.from},
                    dataType: "json",
                    success: function (da) {
                        if(da.error==0){
                            var str = '<li class="pull-left" style="height: 28px; line-height: 28px; margin-bottom:5px; width:95%; " datename='+message.from+'><img style="margin-right:5px;" src="'+da.icon+'" width="28" class="img-circle pull-left"><span class="pull-left hidden-xs">'+da.uname+'</span><span class="pull-left number" style="color:red; font-size: 28px;margin-left:5px;">*</span></li>';
                            $('.ajaxtomember').append(str);
                        }else{
                            alert(da.msg);
                        } 
                    },
                });
            }
        }
        //alert($('.ajaxtomember li').length);
        if($('.ajaxtomember li').length<=1){
            $('.ajaxtomember li').addClass('active');
            $('.ajaxtomember li').find('.number').html('');
            getallmsg();
        }
        
    }

    $('.ajaxexit').click(function(){
        if(confirm('确定退出吗？')){
            window.location.href = '/webim/logout'
        }
        return false;
    });

});    
</script>
@endsection