@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">后台成员管理--添加</h1>
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
                        
                        <div class="form-group  form-inline  col-sm-12" id="selectbtnbody">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="">
                            <input id="selectbtn" class="form-control" type="button" value="浏览">
                            <input class="form-control uploadbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly">
                            <a href="#" class="filenameshow btn" target="_blank" style="display: none;">查看大图</a>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">密码:</label>
                            <input class="form-control" type="password"   style="width:320px;" name="password" placeholder="请输入密码">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">重复密码:</label>
                            <input class="form-control" type="password"  style="width:320px;" name="password2" placeholder="请输入密码">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">手机:</label>
                            <input class="form-control"  style="width:320px;" name="mobile" placeholder="请输入mobile">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">职位:</label>
                            <input class="form-control"  style="width:320px;" name="jobtitle" placeholder="请输入职位">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">性别:</label>
                            <select class="form-control"  style="width:320px;" name="sex">
                                <option value="f">男</option>
                                <option value="m">女</option>
                            </select>
                        </div>

                        <div class="form-group  form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">出生年月:</label>
                            <div class="input-group date form_datetime form-date-box" id="form_datetime" data-date="" data-date-format="yyyy-mm">
                                <input class="form-control"  type="text" value="" name="birthday">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                            </div> 
                        </div>                                                

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">角色:</label>
                            <table style="width:520px;" class="table table-responsive table-striped">                                
                                <tr>
                                    <td>
                                        @foreach($listArr as $v)
                                            <span> <input type="checkbox" name="rids[]"  value="{{$v->id}}">{{$v->name}}</span>
                                        @endforeach                                              
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.adminuser.ajaxadd')}}" type="button" value="保存">
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
@include('admin.common.datetime_js')
@endsection


