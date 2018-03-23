@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">{{$listArr->type=='f'?'聊天群':'参赛队伍'}}--编辑</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.team.'.($listArr->type=='f'?'alist':'index'))}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">                       
                        <input type="hidden" name="id" value="{{$listArr->id}}">
                        <input type="hidden" name="type" value="{{$listArr->type}}">

                        <div class="form-group  form-inline  col-sm-12" id="selectbtnbody">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->icon}}">
                            <input id="selectbtn" class="form-control" type="button" value="浏览">
                            <input class="form-control uploadbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly" value="{{$listArr->icon}}">
                            <a href="{{$listArr->icon}}" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>
                      
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称" value="{{$listArr->name}}">
                        </div>

                        @if($listArr->type=='m')
                            <div class="form-group col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                                <select class="form-control"  style="width:320px;" name="matchid">
                                    @foreach($matchArr as $v)
                                        <option value="{{$v->id}}">{{$v->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group form-inline  col-sm-12">
                                <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                                <select class="form-control"  style="width:120px;" name="province">
                                    <option value="">请选择</option>
                                    @foreach($provinceArr as $v)
                                        <option value="{{$v->name}}" datacode='{{$v->code}}'>{{$v->name}}</option>
                                    @endforeach 
                                </select>

                                @if(in_array($listArr->province,array('北京','天津','上海','重庆')))
                                    <select class="form-control"  style="width:120px;" name="city">
                                        <option value="{{$listArr->city}}"></option>
                                    </select>

                                    <select class="form-control"  style="width:120px;" name="country">
                                        <option value="{{$listArr->city}}">{{$listArr->city}}</option>
                                    </select>
                                @else
                                    <select class="form-control"  style="width:120px;" name="city">
                                        <option value="{{$listArr->city}}">{{$listArr->city}}</option>
                                    </select>

                                    <select class="form-control"  style="width:120px;" name="country">
                                        <option value="{{$listArr->country}}">{{$listArr->country}}</option>
                                    </select>
                                @endif
                            </div>
                        @endif

                        @if(!empty($listArr->teammember))
                            @foreach($listArr->teammember as $key=>$v)
                                <div class="form-group col-sm-12">
                                    <label class="form-label pull-left col-sm-2" style="text-align: right;">@if($key==0) 队员: @endif</label>
                                    <img width="100" height="100"  rwidth="100" rheight="100" src="{{$v->member->icon}}">
                                    <span style="margin:10px; ">{{$v->member->name}}</span> 
                                    <span style="color: #f18181;">@if($v->isleader=='y') {{$listArr->type=='f'?'群主':'队长'}} @endif</span>
                                </div>         
                            @endforeach
                        @endif

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>

                        <div class="form-group form-inline  col-sm-12 ">
                             <label class="form-label pull-left col-sm-2" style="text-align: right;">待选成员:</label>
                             <div class="pull-left col-sm-8 ajaxmember"></div>   
                        </div>
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.team.ajaxedit')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.upload_img_one_js') 
@include('admin.common.area_js')
<script type="text/javascript">
$(document).ready(function (){
    $('select[name=province]').val("{{$listArr->province}}");
    $('select[name=matchid]').val("{{$listArr->matchid}}");

    $.ajax({
        type: "get",
        url: '{{url("admin/team/ajaxgetmember")}}',
        dataType: "html",
        success: function (da) {
            $('.ajaxmember').html(da); 
        },
    });

    $(document).on('click','.ajaxmember li a',function(){
        var page = $(this).html();
        $.ajax({
            type: "get",
            url: '{{url("admin/team/ajaxgetmember")}}?page='+page,
            dataType: "html",
            success: function (da) {
                $('.ajaxmember').html(da); 
            },
        });

        return false;    
    })
    
});
</script> 
@endsection
