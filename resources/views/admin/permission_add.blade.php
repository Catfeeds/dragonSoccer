@extends('admin.common.common')


@section('content')
<link rel="stylesheet" href="/bootstrap-iconpicker/icon-fonts/font-awesome-4.2.0/css/font-awesome.min.css"/>
<link rel="stylesheet" href="/bootstrap-iconpicker/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css"/>
    <section class="content-header">
        <h1 class="pull-left">权限管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.permission.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                       
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">一级权限:</label>
                            <select class="form-control ajaxshowone" dataurl="{{route('admin.permission.ajaxgetcon')}}"  style="width:320px;" name="cid">
                                <option value="0">一级权限</option>
                                @if(!empty($listArr))
                                    @foreach($listArr as $v)
                                        <option value="{{$v->id}}">{{$v->label}}--{{$v->name}}</option>    
                                    @endforeach
                                @endif
                            </select>                            
                        </div> 

                        <div class="form-group col-sm-12" style="display: none;">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">二级权限:</label>
                            <select class="form-control ajaxshowtwo"  style="width:320px;" name="cid2">
                                <option value="0">二级权限</option>
                            </select>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">权限解释名称:</label>
                            <input class="form-control" style="width:320px;" name="label" type="text" placeholder="请输入权限解释名称" value=""> *例如： 权限添加
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">权限名:</label>
                            <input class="form-control" style="width:320px;" name="name" type="text" placeholder="请输入权限名" value=""> *例如： admin.permission.add
                        </div>
                        
                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">描述与备注:</label>
                            <textarea class="form-control" style="width:320px;" name="description"  placeholder="请输入描述与备注"></textarea>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">图标:</label>
                            <button class="btn btn-default" name="icon" data-iconset="fontawesome" data-icon="fa-sliders" role="iconpicker"></button> * 一级 二级权限请图标必填 
                        </div> 

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.permission.ajaxadd')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
<script type="text/javascript" src="/bootstrap-iconpicker/bootstrap-iconpicker/js/iconset/iconset-fontawesome-4.3.0.min.js"></script>
<script type="text/javascript" src="/bootstrap-iconpicker/bootstrap-iconpicker/js/bootstrap-iconpicker.js"></script>

<script type="text/javascript">
$(document).ready(function (){
var cid = '{{$cid}}';
var cid2 = '{{$cid2}}';
$(".ajaxshowone").val(cid);
@if($cid2 > 0)
$.ajax({
    type: "get",
    url: $('.ajaxshowone').attr('dataurl'),
    data:{'id':$('.ajaxshowone').val()},
    dataType: "json",
    success: function (da) {                
        if (da.error == 0) {
            var str = '<option value="0">二级权限</option>';
            console.log(str);
            $.each(da.data,function(i,d){
                str +=('<option value="'+d.id+'">'+d.label+'--'+d.name+'</option>');
            })
            $('.ajaxshowtwo').html(str).parent().show();
            $('.ajaxshowtwo').val(cid2);
        }
    },
});
@endif


//表单提交
$(".ajaxshowone").on("change",function(){
    if($(this).val() >0 ){    
        $.ajax({
            type: "get",
            url: $(this).attr('dataurl'),
            data:{'id':$(this).val()},
            dataType: "json",
            success: function (da) {                
                if (da.error == 0) {
                    var str = '<option value="0">二级权限</option>';
                    console.log(str);
                    $.each(da.data,function(i,d){
                        str +=('<option value="'+d.id+'">'+d.label+'--'+d.name+'</option>');
                    })
                    console.log(str);
                    $('.ajaxshowtwo').html(str).parent().show();
                }
            },
        });
    }else{
        $('.ajaxshowtwo').html('<option value="0">二级权限</option>').parent().hide();
    }
    return false;            
})

});    
</script>
@endsection
