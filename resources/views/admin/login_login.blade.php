<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>管理后台</title>
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>

    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/bower_components/select2/select2.min.css">
    <link rel="stylesheet" href="/bower_components/admin-lte/AdminLTE.min.css">
    <link rel="stylesheet" href="/bower_components/admin-lte/_all-skins.min.css">
    <link rel="stylesheet" href="/bower_components/ionicons/ionicons.min.css">
    <link rel="stylesheet" href="/css/error-alert.css">
</head>

<body class="skin-blue sidebar-mini">
<div class="container">
    <div class="row" style="margin-top:15%;">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">登录后台</div>
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


<!-- jQuery 2.1.4 -->
<script src="/bower_components/jquery/dist/jquery.min.js"></script>
<script type="text/javascript">
//表单提交
$(".ajaxsubmit").on("click",function(){    
    $.ajax({
        type: "POST",
        url: "/admin/ajaxlogin",
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
</script>
</body>
</html>




