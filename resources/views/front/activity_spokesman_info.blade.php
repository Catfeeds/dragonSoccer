@extends('front.common.activity_common')
@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
@endsection

@section('content')
    <!--顶部-->
    <div class="cell">
        <img src="{{$listArr->member->icon}}" class="headerImage"/>
        <ul class="mx_ul">
            <li>
                <p class="persontitle am-text-top">{{$listArr->member->name}}：{{empty($listArr->allnumber)?0:$listArr->allnumber }}票</p>
            </li>
            <li>
                <div class="am-progress am-progress-xs progress_Mx">
                    <div class="am-progress-bar" style="width: {{empty($listArr->percent)?0:$listArr->percent}}%"></div>
                </div>
            </li>
        </ul>
        {{empty($listArr->percent)?0:$listArr->percent}}%
        <!-- <input class="upforbutton" style="color:#F35D5D" type="button" value="举报"> -->
    </div>

    <div class="container">
        <div class="col-md-12">
            {{empty($listArr)?'':$listArr->txt}}
        </div>
        <div class="col-md-12">
            @if(!empty($listArr->imgs))
                @foreach($listArr->imgs as $v)
                    <img style="width:100%;" src="{{empty($v)?'':$v}}"><br>
                @endforeach
            @endif 
        </div>   
    </div>

    <hr/>
    <a><button class="navbar-fixed-bottom sureBtn ajaxpopcontent" data='o' data-toggle="modal" data-target="#deleModal" dataname='{{$listArr->member->name}}'  datamid='{{$listArr->mid}}'>投票</button></a>

    <!-- 投票弹窗 -->
    <div class="modal fade" id="deleModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">帮<span class="ajaxname">xx</span>投票</h4>
                </div>
                <div class="modal-body">
                    <p>1龙珠可投1票</p>
                    <p>
                        <input class="form-control ajaxnumber" type="number" name="" value="1">
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="modal-btn-no ajaxcloseall" data-dismiss="modal">取消</button>
                    <button type="button" class="modal-btn-yes ajaxformsubmit">确定</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
@include('front.common.activity_login_js')
<script type="text/javascript">
$(document).ready(function (){
    $(".ajaxpopcontent").on("click",function(){ 
        var dataname = $(this).attr('dataname');
        var datamid = $(this).attr('datamid');
        $('.ajaxname').html(dataname);
        $('.ajaxnumber').val(1);

        $(".ajaxformsubmit").on("click",function(){    
            $.ajax({
                type: "POST",
                url: '{{url("activity/spokesman/ajaxsupport?")}}'+parameter,
                data:{'bestmid':datamid,'number':$('.ajaxnumber').val()},
                dataType: "json",
                headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
                success: function (da) {
                    sendError(da.msg);
                    if (da.error == 0) {
                        window.location.reload(); 
                    }
                },
            });
            return false;            
        })            
    })
})  
</script>

@endsection