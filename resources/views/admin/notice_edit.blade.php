@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">赛事管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.adminuser.index')}}">返回</a>  
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
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">标题:</label>
                            <input class="form-control"  style="width:320px;" name="title" placeholder="请输入标题" value="{{$listArr->title}}">
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">排序:</label>
                            <input class="form-control"  style="width:320px;" type="number" name="rsort" placeholder="请输入排序" value="{{$listArr->rsort}}"><span style="color:red;">* 数字越大越靠前</span>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">状态:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="n">待发布</option>
                                <option value="y">已发布</option>
                            </select>
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">规则:</label>
                            <script class="form-label pull-left col-sm-8" id="container" name="content" type="text/plain">{!! $listArr->content !!}</script>
                        </div>
                        

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.notice.ajaxedit')}}" type="button" value="保存">
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
        $('select[name=status]').val("{{$listArr->status}}");
    });
</script>  
@endsection
