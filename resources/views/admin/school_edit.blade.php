@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">主办方--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.school.index')}}">返回</a>  
        </h1>
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
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称" value="{{$listArr->name}}">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">登陆名:</label>
                            <input class="form-control"  style="width:320px;" name="loginname" placeholder="请输入登陆名" value="{{$listArr->loginname}}">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">密码:</label>
                            <input class="form-control" type="password"   style="width:320px;" name="pwd" placeholder="请输入密码">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">类型:</label>
                            <select class="form-control"  style="width:320px;" name="type">
                                <option value="s">校园</option>
                                <option value="l">官方</option>
                            </select>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.school.ajaxedit')}}" type="button" value="保存">
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
        $('select[name=type]').val("{{$listArr->type}}");
    });
</script> 
@endsection


