@extends('front.common.activity_common')

@section('css')
    <link href="/css/spokesPersonCss.css" rel="stylesheet">
@endsection

@section('content')

    <!--标题-->
    <p class="title">{{$spokesmanArr['title']}}({{$sex}})</p>
    <p class="context">{{empty($listArr)?0:count($listArr)}}人参与</p>
    <p class="timeText">{{$spokesmanArr['time']}}</p>
    <hr/>
    <!--标题end-->

    <!--参赛球员-->
    <p class="title">参赛球员</p>

    @if(!empty($listArr))
        @foreach($listArr as $v)
            <div class="cell">
                <a href="javascript:;" class="ajaxurl" href="javascript:;"  dataurl="{{url('activity/spokesman/info/'.$v->id.'?')}}"><img src="{{$v->member->icon}}" class="headerImage"/></a>
                <ul class="mx_ul">
                    <li>
                        <p class="persontitle am-text-top">{{$v->member->name}}：{{empty($v->allnumber)?0:$v->allnumber }}票</p>
                    </li>
                    <li>
                        <div class="am-progress am-progress-xs progress_Mx">
                            <div class="am-progress-bar" role="progressbar" aria-valuenow="2" aria-valuemin="0" aria-valuemax="100"  style="min-width: 2em;width: {{empty($v->percent)?0:$v->percent}}%">
                                {{empty($v->percent)?0:$v->percent}}%
                            </div>
                        </div>
                    </li>
                </ul>
                {{--<span style="font-size:8px; ">{{empty($v->percent)?0:$v->percent}}%</span>--}}
                <input class="upforbutton ajaxpopcontent" type="button" value="支持" data='o' data-toggle="modal" data-target="#deleModal" dataname='{{$v->member->name}}'  datamid='{{$v->mid}}'>
            </div>
        @endforeach
    @else
        无
    @endif

    @if(empty($memArr))
        <a class="ajaxurl" href="javascript:;"  dataurl="{{url('/activity/spokesman/apply?')}}"><button class="navbar-fixed-bottom sureBtn">参加活动</button></a>
    @endif

    @if(!empty($memArr) && $memArr->status=='w')
        <a class="ajaxurl" href="javascript:;"  dataurl="{{url('/activity/spokesman/apply?id='.$memArr->id.'&')}}"><button class="navbar-fixed-bottom sureBtn">修改信息</button></a>
    @endif

    @if(!empty($memArr) && $memArr->status!='w')
        <a class="ajaxurl" href="javascript:;"  dataurl="{{url('/activity/spokesman/info/'.$memArr->id.'?')}}"><button class="navbar-fixed-bottom sureBtn">查看详情</button></a>
    @endif
    


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
                    if(da.error == 9){
                        alert(da.msg);
                    }else{
                        sendError(da.msg);
                        if (da.error == 0) {
                            window.location.reload(); 
                        }
                    }
                },
            });
            return false;            
        })            
    })  

    
})  
</script>

@endsection
