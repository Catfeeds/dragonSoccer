@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛程-添加</h1>        
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">    
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                            <select class="form-control"  style="width:320px;" name="matchid">
                                <option value="">请选择</option>
                                @foreach($gamesArr as $v)
                                    <option value="{{$v->id}}">{{$v->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">轮次:</label>
                            <select class="form-control"  style="width:320px;" name="matchlevel">
                                <option value="">请选择</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">年龄:</label>
                            <select class="form-control"  style="width:320px;" name="gamesagesid">
                               
                            </select>
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">组名:</label>
                            <input class="form-control"  style="width:320px;" name="groupsn" placeholder="请输入组名" value="">
                        </div>                           

                        <div class="form-group form-inline  col-sm-12 ">
                             <label class="form-label pull-left col-sm-2" style="text-align: right;">待选成员:</label>
                             <div class="pull-left col-sm-8">
                                 <table class="table table-responsive table-striped" id="videos-table">
                                    <thead style="background-color:#F5F5F5;">
                                        <th width="5%"></th>
                                        <th width="5%">ID</th>
                                        <th width="80%">名称->手机号-出生日期-报名时间</th>
                                    </thead>
                                    <tbody class="ajaxbody">
                                        
                                    </tbody> 
                                </table>
                             </div>   
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">A:</label>
                            <input class="form-control"  style="width:320px;" name="ateamid" placeholder="请输入队名" value="">
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">B:</label>
                            <input class="form-control"  style="width:320px;" name="bteamid" placeholder="请输入队名" value="">
                        </div> 

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">时间:</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd HH:ii">
                                        <input class="form-control" size="16" type="text" value="" name="stime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                            <input class="form-control"  style="width:320px;" name="address" placeholder="请输入地址" value="">
                        </div> 

                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.gamelog.ajaxadd')}}" type="button" value="保存">
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
    $("select[name=matchid]").on("change",function(){  
        $.ajax({
            type: "get",
            url: '{{route("admin.gamelog.ajaxages")}}'+'?gid='+$(this).find("option:selected").val(),
            dataType: "html",
            success: function (html) {
                $("select[name=gamesagesid]").html(html);
            },
        });

        $.ajax({
            type: "get",
            url: '{{route("admin.gamelog.ajaxteamruler")}}'+'?gid='+$(this).find("option:selected").val(),
            dataType: "html",
            success: function (html) {
                $("select[name=matchlevel]").html(html);
            },
        });

        return false;            
    })

    $("select[name=gamesagesid]").on("change",function(){  
        $.ajax({
            type: "get",
            url: '{{route("admin.gamelog.ajaxteam")}}'+'?gaid='+$(this).find("option:selected").val(),
            dataType: "html",
            success: function (html) {
                $(".ajaxbody").html(html);
            },
        });
        return false;            
    })
});    
</script>
@endsection
