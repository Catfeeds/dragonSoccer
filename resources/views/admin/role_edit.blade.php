@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">角色管理--修改</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.role.index')}}">返回</a>  
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
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">角色名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入角色名称" value="{{$listArr->name}}">
                        </div>
                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">描述与备注:</label>
                            <textarea class="form-control" style="width:320px;" name="description"  placeholder="请输入描述与备注">{{$listArr->description}}</textarea>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>

                        @foreach($cidArr as $v)
                        <div class="form-group form-inline  col-sm-12 ajaxdiv">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"> <input class="ajaxone"  @if(in_array($v->id,$permissionsArr)) checked='checked' @endif  type="checkbox" name="pids[]"  value="{{$v->id}}" style="display:none; ">{{$v->label}}:</label>
                            <table style="width:520px;" class="table table-responsive table-striped">
                                @foreach($v->sons as $vv)
                                    <tr class="ajaxtr">
                                        <td><input class="ajaxtwo" type="checkbox" name="pids[]" @if(in_array($vv->id,$permissionsArr)) checked='checked' @endif value="{{$vv->id}}">{{$vv->label}}</td>
                                        <td>
                                            @foreach($vv->sons as $vvv)
                                                <span> <input class="ajaxthree" type="checkbox" name="pids[]" @if(in_array($vvv->id,$permissionsArr)) checked='checked' @endif  value="{{$vvv->id}}">{{$vvv->label}}</span>
                                            @endforeach                                              
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                        @endforeach

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.role.ajaxedit')}}" type="button" value="保存">
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
    $('.ajaxthree').on('click',function(){
        if($(this).is(':checked')){
            $(this).parents('.ajaxdiv').find('.ajaxone').attr('checked',true);
            $(this).parents('.ajaxtr').find('.ajaxtwo').attr('checked',true);
        }
    })

    $('.ajaxtwo').on('click',function(){
        if($(this).is(':checked')){
            $(this).parents('.ajaxdiv').find('.ajaxone').attr('checked',true);
        }
    }) 
})
</script>
@endsection
