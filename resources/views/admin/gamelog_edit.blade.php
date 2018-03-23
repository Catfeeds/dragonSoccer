@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛程--编辑</h1>        
    </section>
    <div class="clearfix"></div>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <input type="hidden" name="id" value="{{$listArr->id}}">

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                            {{$listArr->gamesages->games->name}}
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">轮次:</label>
                            {{$listArr->matchlevel}}
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">年龄:</label>
                            {{date('Y/m/d',$listArr->gamesages->starttime)}}-{{date('Y/m/d',$listArr->gamesages->endtime)}}
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">组名:</label>
                            <input class="form-control"  style="width:320px;" name="groupsn" placeholder="请输入组名" value="{{$listArr->groupsn}}">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">A:</label>
                            <div class="input-group"   style="width:520px;" >
                                <input class="form-control" name="ateamid" placeholder="请输入队名" value="{{$listArr->ateamid}}">
                                <span class="input-group-addon">{{empty($listArr->ateam)?'':$listArr->ateam->name}}</span>
                            </div> 
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">B:</label>
                            <div class="input-group"   style="width:520px;" >
                                <input class="form-control" name="bteamid" placeholder="请输入队名" value="{{$listArr->bteamid}}">
                                <span class="input-group-addon">{{empty($listArr->bteam)?'':$listArr->bteam->name}}</span>
                            </div> 
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">时间:</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd HH:ii">
                                        <input class="form-control" size="16" type="text" value="{{empty($listArr->stime)?'':date('Y-m-d H:i',$listArr->stime)}}" name="stime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                            <input class="form-control"  style="width:320px;" name="address" placeholder="请输入地址" value="{{empty($listArr->address)?'':$listArr->address}}">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">A得分:</label>
                            <input class="form-control"  style="width:320px;" name="ateamscore" placeholder="请输入得分" value="{{$listArr->ateamscore}}">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">B得分:</label>
                            <input class="form-control"  style="width:320px;" name="bteamscore" placeholder="请输入得分" value="{{$listArr->bteamscore}}">
                        </div>
                        
                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">获胜队伍:</label>
                            <select class="form-control"  style="width:320px;" name="successteamid">
                                    <option value="">请选择</option>
                                    <option value="all">全部淘汰</option>
                                    <option value="{{$listArr->ateamid}}">A</option>
                                    <option value="{{$listArr->bteamid}}">B</option>
                            </select>
                        </div>
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.gamelog.ajaxedit')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.datetime_js')
<script type="text/javascript">
$(document).ready(function (){
    $('select[name=successteamid]').val("{{$listArr->successteamid}}");
    $.ajax({
        type: "get",
        url: '{{route("admin.gteam.ajaxgroup")}}'+'?gaid='+'{{$listArr->gamesages->id}}',
        dataType: "html",
        success: function (html) {
            $(".ajaxbody").html(html);
        },
    });        
});    
</script>
@endsection
