@extends('admin.common.common')

@section('content')
    <section class="content-header">
        <h1 class="pull-left">会员管理--添加</h1>
        <h1 class="pull-right">
            <a class="btn btn-primary pull-right" style="margin-top: -10px;margin-bottom: 5px" href="{{route('admin.member.index')}}">返回</a>  
        </h1>
    </section>
    <div class="content">
        <div class="clearfix"></div>
        <div class="box box-primary">
           <div class="box-body">
               <div class="row">
                    <form id="subform" method="POST" action="" accept-charset="UTF-8">                       
                        <input type="hidden" name="id" value="{{$listArr->id}}">

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody0">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">头像:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->icon}}">
                            <input id="selectbtn0" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="icon" readonly="readonly"  value="{{$listArr->icon}}">
                            <a href="{{$listArr->icon}}" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">名称:</label>
                            <input class="form-control"  style="width:320px;" name="name" placeholder="请输入名称" value="{{$listArr->name}}">
                        </div>
                       
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">手机:</label>
                            <input class="form-control"  style="width:320px;" name="mobile" placeholder="请输入mobile"  value="{{$listArr->mobile}}" >
                        </div>                 

                        <div class="form-group form-inline  col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">地址:</label>
                            <select class="form-control"  style="width:120px;" name="province">
                                <option value="{{$listArr->province}}">{{$listArr->province}}</option>
                                @foreach($provinceArr as $v)
                                    <option value="{{$v->name}}" datacode='{{$v->code}}'>{{$v->name}}</option>
                                @endforeach 
                            </select>

                            <select class="form-control"  style="width:120px;" name="city">
                                <option value="{{$listArr->city}}">{{$listArr->city}}</option>
                            </select>

                            <select class="form-control"  style="width:120px;" name="country">
                                <option value="{{$listArr->country}}">{{$listArr->country}}</option>
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">详细地址:</label>
                            <input class="form-control"  style="width:320px;" name="address" placeholder="请输入详细地址" value="{{$listArr->address}}">
                        </div>
                        
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">真实姓名:</label>
                            <input class="form-control"  style="width:320px;" name="truename" placeholder="请输入真实姓名" value="{{$listArr->truename}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">民族:</label>
                            <input class="form-control"  style="width:320px;" name="nation" placeholder="请输入民族" value="{{$listArr->nation}}">
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证号:</label>
                            <input class="form-control"  style="width:320px;" name="idnumber" placeholder="请输入身份证号" value="{{$listArr->idnumber}}">
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">出生年月:</label>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="birthday"  value="{{$listArr->birthday}}">
                        </div>   
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">性别:</label>
                             <select class="form-control"  style="width:320px;" name="sex">
                                <option value="f">男</option>
                                <option value="m">女</option>
                            </select>
                        </div>

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody1">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证正面:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->idcard_f}}">
                            <input id="selectbtn1" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="idcard_f" readonly="readonly" value="{{$listArr->idcard_f}}">
                            <a href="{{$listArr->idcard_f}}" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody2">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证反面:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->idcard_b}}">
                            <input id="selectbtn2" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="idcard_b" readonly="readonly" value="{{$listArr->idcard_b}}">
                            <a href="{{$listArr->idcard_b}}" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身份证地址:</label>
                            <input class="form-control"  style="width:320px;" name="idcard_address" placeholder="请输入详细地址" value="{{$listArr->idcard_address}}">
                        </div>

                        <div class="form-group  form-inline  col-sm-12 selectbtnbody" id="selectbtnbody3">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">个人照片:</label>
                            <img class="uploadimg"  width="100" height="100"  rwidth="100" rheight="100" src="{{$listArr->img}}">
                            <input id="selectbtn3" class="form-control selectbtn" type="button" value="上传">
                            <span class="uploadfilename"></span>
                            <span class="uploadspan"></span>
                            <input class="uploadinput form-control"  style="width:320px;" type="text" name="img" readonly="readonly" value="{{$listArr->img}}">
                            <a href="{{$listArr->img}}" class="filenameshow btn" target="_blank">查看大图</a>
                        </div>


                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">所在学校:</label>
                            <input class="form-control"  style="width:320px;" name="school" placeholder="请输入所在学校" value="{{$listArr->school}}">
                        </div>
                        
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">擅长位置:</label>
                            <select class="form-control"  style="width:320px;" name="position">
                                @foreach($memberArr['positionArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">管用脚:</label>
                            <select class="form-control"  style="width:320px;" name="foot">
                                @foreach($memberArr['footArr'] as $k=>$v)
                                    <option value="{{$k}}">{{$v}}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">简介:</label>
                            <textarea name="instruction" class="form-control"  style="width:320px; height:100px;" placeholder="请输入简介">
                                {{$listArr->instruction}}    
                            </textarea>
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">身高:</label>
                            <input class="form-control"  style="width:160px;" name="height" placeholder="请输入身高" value="{{$listArr->height}}"> cm
                        </div>

                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">体重:</label>
                            <input class="form-control"  style="width:160px;" name="weight" placeholder="请输入体重" value="{{$listArr->weight}}"> kg
                        </div>


                        <div class="form-group form-inline col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;">status:</label>
                            <select class="form-control"  style="width:320px;" name="status">
                                <option value="n">未认证</option>
                                <option value="y">已认证</option>
                            </select>
                        </div>

                        <div class="form-group form-inline  col-sm-12">
                            <hr>
                        </div>
                        <div class="form-group col-sm-12">
                            <label class="form-label pull-left col-sm-2" style="text-align: right;"></label>
                            <input class="btn btn-primary ajaxformsubmit" dataurl="{{route('admin.member.ajaxedit')}}" type="button" value="保存">
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
@include('admin.common.area_js')
<script type="text/javascript">
    $(document).ready(function (){
        $('select[name=province]').val("{{$listArr->province}}");
        $('select[name=position]').val("{{$listArr->position}}");
        $('select[name=foot]').val("{{$listArr->foot}}");
        $('select[name=status]').val("{{$listArr->status}}");
        $('select[name=sex]').val("{{$listArr->sex}}");
    });
</script>  
@endsection
