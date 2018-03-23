@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">推广列表--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.company.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名字:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名字">
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">关键字:</label>
                            <input class="form-control" type="text"  style="width:320px;" name="key" placeholder="请输入关键字" value="">
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">url:</label>
                            <input class="form-control" type="text"  style="width:320px;" name="url" placeholder="请输入关键字" value="">
                        </div>

                                              

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.company.ajaxadd')}}" type="button" value="保存">
                            <a href="" class="btn btn-default">取消</a>
                        </div>
                    </form>
               </div>
           </div>
       </div>
   </div>
@endsection

@section('scripts')
@include('admin.common.ueditor_js')
<script type="text/javascript">
    $(document).ready(function (){
        var url = "{{url('/invite?recommend=')}}";
        $('input[name=key]').blur(function(){
            $('input[name=url]').val(url+$(this).val());
        })
        
    });
</script>
@endsection


