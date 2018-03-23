@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛事报名-添加</h1>        
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <input type="hidden" name="id" value="{{$gArr->id}}">    
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                            {{$gArr->gamesages->games->name}}
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">年龄:</label>
                            {{date('Y/m/d',$gArr->gamesages->starttime)}}-{{date('Y/m/d',$gArr->gamesages->endtime)}}
                        </div> 

                        <div class="form-group form-inline  col-sm-12 ">
                             <label class="form-label pull-left col-sm-2" style="text-align: right;">成员:</label>
                             <div class="pull-left col-sm-8">
                                 <table class="table table-responsive table-striped" id="videos-table">
                                    <thead style="background-color:#F5F5F5;">
                                        <th width="5%">ID</th>
                                        <th width="10%">名称</th>
                                        <th width="10%">手机号</th>
                                        <th width="10%">出生日期</th>
                                        <th width="10%">报名时间</th>
                                        <th width="10%">操作</th>
                                    </thead>
                                    <tbody>
                                        @if(!empty($gmArr))
                                            @foreach($gmArr as $v)
                                                <tr>
                                                    <td>{{$v->id}}</td>
                                                    <td>{{$v->members->name}}</td>
                                                    <td>{{$v->members->mobile}}</td>       
                                                    <td>{{$v->members->birthday}}</td>       
                                                    <td>{{$v->created_at}}</td>
                                                    <td>
                                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.group.ajaxdelmember')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
                                                    </td>
                                                </tr>                            
                                            @endforeach
                                        @endif    
                                    </tbody> 
                                </table>
                             </div>   
                        </div>
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>                           

                        <div class="form-group form-inline  col-sm-12 ">
                             <label class="form-label pull-left col-sm-2" style="text-align: right;">待选成员:</label>
                             <div class="pull-left col-sm-8">
                                 <table class="table table-responsive table-striped" id="videos-table">
                                    <thead style="background-color:#F5F5F5;">
                                        <th width="5%"></th>
                                        <th width="5%">ID</th>
                                        <th width="10%">名称</th>
                                        <th width="10%">手机号</th>
                                        <th width="10%">出生日期</th>
                                        <th width="10%">注册时间</th>
                                        <th width="10%">推荐人</th>
                                        <th width="10%">认证状态</th>
                                    </thead>
                                    <tbody class="ajaxmember">
                                        
                                    </tbody> 
                                </table>
                             </div>   
                        </div>
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.group.ajaxaddmember')}}" type="button" value="添加成员">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function (){

    $.ajax({
        type: "get",
        url: '{{route("admin.group.ajaxmember")}}'+'?gaid='+'{{$gArr->gamesages->id}}',
        dataType: "html",
        success: function (html) {
            $(".ajaxmember").html(html);
        },
    });         
 

    $(document).on('click','.ajaxmember li a',function(){
        var page = $(this).html();
        $.ajax({
            type: "get",
            url: '{{route("admin.group.ajaxmember")}}?page='+page+'&gaid='+'{{$gArr->gamesages->id}}',
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
