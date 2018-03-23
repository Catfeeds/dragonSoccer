@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">参赛队伍--添加</h1>        
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <input type="hidden" name="id" value="{{$listArr->id}}">  

                        <div class="form-group  form-inline  col-sm-12" id="selectbtnbody">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->icon}}">
                            <input id="selectbtn" class="form-control" type="button" value="浏览">
                            <input class="form-control uploadbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly" value="{{$listArr->icon}}">
                            <a href="#" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>
                      
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称" value="{{$listArr->name}}">
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">比赛:</label>
                            {{$listArr->gamesages->games->name}}
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">年龄:</label>
                            {{date('Y/m/d',$listArr->gamesages->starttime)}}-{{date('Y/m/d',$listArr->gamesages->endtime)}}
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
                                                    <td>{{$v->member->name}}</td>
                                                    <td>{{$v->member->mobile}}</td>       
                                                    <td>{{$v->member->birthday}}</td>       
                                                    <td>{{$v->created_at}}</td>
                                                    <td>
                                                        <a href="javascript:;" class='btn btn-default btn-xs ajaxbtnsubmit' dataurl="{{route('admin.gteam.ajaxdelmember')}}" dataid='{{$v->id}}'><i class="glyphicon glyphicon-trash"></i></a>
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
                                        <th width="5%">数量</th>
                                        <th width="80%">名称->手机号-出生日期-报名时间</th>
                                    </thead>
                                    <tbody class="ajaxbody">
                                        
                                    </tbody> 
                                </table>
                             </div>   
                        </div>
                       
                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.gteam.ajaxedit')}}" type="button" value="保存">
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
<script type="text/javascript">
$(document).ready(function (){
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
