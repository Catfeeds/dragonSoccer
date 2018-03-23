@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛事管理--编辑</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.games.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <input type="hidden" name="id" value="{{$listArr->id}}">
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称"  value="{{$listArr->name}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">排序:</label>
                            <input class="form-control"  style="width:320px;" name="sid" placeholder="请输入排序"  value="{{$listArr->sid}}">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">简介:</label>
                            <textarea class="form-control"  style="width:320px;" name="info" placeholder="请输入简介">{{$listArr->info}}</textarea>
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">报名时间:</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text"  value="{{empty($listArr->applystime)?'':date('Y-m-d',$listArr->applystime)}}" name="applystime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                                    <input class="form-control" size="16" type="text" value="{{empty($listArr->applyetime)?'':date('Y-m-d',$listArr->applyetime)}}" name="applyetime">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div> 
                            </div>
                        </div>


                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">比赛时间:</label>
                            <div class="form-group">
                                <div class="input-group">
                                    <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime" data-date="" data-date-format="yyyy-mm-dd">
                                        <input class="form-control" size="16" type="text" value="{{empty($listArr->starttime)?'':date('Y-m-d',$listArr->starttime)}}" name="starttime">
                                        <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group date form_datetime form-date-box pull-left" id="form_datetime1" data-date="" data-date-format="yyyy-mm-dd">
                                    <input class="form-control" size="16" type="text" value="{{empty($listArr->endtime)?'':date('Y-m-d',$listArr->endtime)}}" name="endtime">
                                    <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                </div> 
                            </div>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">主办方:</label>
                            <select class="form-control ajaxages"  style="width:320px;" name="owner">
                                <option value="">请选择</option>
                                @foreach($schoolArr as $k=>$v)
                                    <option value="{{$v->id}}">{{$v->name}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">赛制:</label>
                            <select class="form-control ajaxruler"  style="width:320px;" name="rulernum">
                                <option value="">请选择</option>
                                <option value="3">3</option>
                                <option value="2">2</option>
                                <option value="1">1</option>
                            </select>
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">赛制详情：</label>
                            <div class="form-group form-inline col-sm-11">
                                <div class="form-group col-sm-1">级别</div>    
                                <div class="form-group col-sm-1">队伍数量</div>    
                                <div class="form-group col-sm-1">晋级指数</div> 
                                <div class="form-group col-sm-1">补充人数</div> 
                                <div class="form-group col-sm-4">补充人员时间</div> 
                                <div class="form-group col-sm-4">轮次-时间</div>     
                            </div>
                            <div class="ajaxrulercontent"></div>
                        </div>

                        <div class="form-group col-sm-12 ">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">参赛年龄:</label>
                            <div class="ajaxagescontent"></div>
                            
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;">状态:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="n">待发布</option>
                                <option value="y">已发布</option>
                            </select>
                        </div>

                        @for($i=0;$i<=5;$i++)
                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody{{$i}}">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">@if($i==0) 赛事图片: @endif</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{(empty($listArr->imgArr)||empty($listArr->imgArr[$i]))?'':$listArr->imgArr[$i]}}">
                            <input id="selectbtn{{$i}}" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="imgs[]" readonly="readonly" value="{{(empty($listArr->imgArr)||empty($listArr->imgArr[$i]))?'':$listArr->imgArr[$i]}}">
                            <a href="{{(empty($listArr->imgArr)||empty($listArr->imgArr[$i]))?'':$listArr->imgArr[$i]}}" class="filenameshow btn" target="_blank" {{(empty($listArr->imgArr)||empty($listArr->imgArr[$i]))?'':'style="display: none;"'}} >查看大图</a>
                        </div>
                        @endfor

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-1" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.games.ajaxedit')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.upload_img_more_js')
@include('admin.common.datetime_js')

<script type="text/javascript">
$(document).ready(function (){
    $('select[name=owner]').val("{{$listArr->owner}}");
    $('select[name=rulernum]').val("{{$listArr->ruler}}");
    $('select[name=status]').val("{{$listArr->status}}");
    $.ajax({
        type: "get",
        url: '{{route("admin.games.ajaxages")}}'+'?gid='+"{{$listArr->id}}"+'&ownerid='+"{{$listArr->owner}}",
        dataType: "html",
        success: function (html) {
            $(".ajaxagescontent").html(html);
        },
    });

    $.ajax({
        type: "get",
        url: '{{route("admin.games.ajaxruler")}}'+'?gid='+"{{$listArr->id}}"+'&ruler='+"{{$listArr->ruler}}",
        dataType: "html",
        success: function (html) {
            $(".ajaxrulercontent").html(html);
        },
    });


    $(".ajaxages").on("change",function(){  
        $.ajax({
            type: "get",
            url: '{{route("admin.games.ajaxages")}}'+'?gid='+"{{$listArr->id}}"+'&ownerid='+$(this).find("option:selected").val(),
            dataType: "html",
            success: function (html) {
                $(".ajaxagescontent").html(html);
            },
        });
        return false;            
    })

    $(".ajaxruler").on("change",function(){  
        $.ajax({
            type: "get",
            url: '{{route("admin.games.ajaxruler")}}'+'?gid='+$('.ajaxages').find("option:selected").val()+'&ruler='+$(this).find("option:selected").val(),
            dataType: "html",
            success: function (html) {
                $(".ajaxrulercontent").html(html);
            },
        });
        return false;            
    })

    $(document).on('blur','.ajaxrulerinfo',function(){
        th = $(this);
        grid = $(this).attr('dataid');
        number = $(this).val();
        rulerkey = $(this).attr('datakey');
        $.ajax({
            type: "get",
            url: '{{route("admin.games.ajaxrulerinfo")}}',
            data:{'grid':grid,'number':number,'rulerkey':rulerkey},
            dataType: "html",
            success: function (html) {
                th.parents('.ajaxbody').find(".ajaxrulerinfocontent").html(html);
            },
        });
        return false;    
    })
});    
</script>
@endsection


