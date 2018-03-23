@extends('webim.common')
@section('css')
<link rel="stylesheet" type="text/css" href="/webim/webim.css">
@endsection
@section('content')
<div class="container">
    <div class="row" style="margin-top:15%;">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">登录客服聊天系统</div>
                <div class="panel-body">
                    <form class="form-horizontal" role="form" method="POST">
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="mobile" class="col-md-4 control-label">手机号</label>

                            <div class="col-md-6">
                                <input id="mobile" type="mobile" class="form-control" name="mobile" value="{{ old('mobile') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password" class="col-md-4 control-label">密码</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control" name="password">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="button" class="btn btn-primary ajaxsubmit">
                                    <i class="fa fa-btn fa-sign-in"></i> 登录
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>   
@endsection

@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $(".ajaxsubmit").on("click",function(){    
        $.ajax({
            type: "POST",
            url: "/webim/ajaxlogin",
            data:$(this).parents('form').serialize(),
            dataType: "json",
            success: function (da) {
                alert(da.msg);
                if (da.error == 0) {
                    window.location.href = da.url; 
                }
            },
        });
        return false;            
    })    
});    
</script>
@endsection


