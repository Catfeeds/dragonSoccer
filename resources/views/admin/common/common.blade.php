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

    <!-- Ionicons -->
    <link rel="stylesheet" href="/bower_components/ionicons/ionicons.min.css">

    <link rel="stylesheet" href="/css/error-alert.css">

    <link href="/dist/css/load/load.css" rel="stylesheet">
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    @yield('css')
</head>

<body class="skin-blue sidebar-mini">
    <div id="loading" style="display: block;">
        <div id="loading-center">
            <div id="loading-center-absolute">
                <div class="object" id="object_four"></div>
                <div class="object" id="object_three"></div>
                <div class="object" id="object_two"></div>
                <div class="object" id="object_one"></div>
            </div>
        </div>
    </div>    

    <div class="wrapper">
        <header class="main-header">
            <a href="{{url('/admin')}}" class="logo">
                <i class="glyphicon glyphicon-home"></i> <b>LZSN</b>
            </a>

            <nav class="navbar navbar-static-top" role="navigation">
                <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                    <span class="sr-only"></span>
                </a>
                <!-- Navbar Right Menu -->
                <div class="navbar-custom-menu">
                    <ul class="nav navbar-nav">
                        <li>
                            <a href="">{{auth()->guard('adminusers')->user()->name}}</a> 
                        </li>
                        <li>
                            <a href="{!! url('admin/logout') !!}" class="btn">退出</a> 
                        </li>
                    </ul>
                </div>
            </nav>
        </header>

        @include('admin.common.sidebar')  <!-- 左侧菜单 -->

        <div class="content-wrapper">
            @yield('content')
        </div>

        <!-- Main Footer -->
        <footer class="main-footer" style="max-height: 100px;text-align: center">
            <strong>Copyright © 2017 <a href="#">龙之少年</a>.</strong> All rights reserved.
        </footer>

    </div>



<!-- jQuery 2.1.4 -->

<script src="/bootstrap/js/bootstrap.min.js"></script>
<script src="/bower_components/select2/select2.js"></script>
<script src="/bower_components/iCheck/icheck.min.js"></script>
<!-- AdminLTE App -->
<script src="/bower_components/admin-lte/app.min.js"></script>
<!-- Qiniu 1.0.14-->
<script src="http://cdn.staticfile.org/qiniu-js-sdk/1.0.14-beta/qiniu.min.js"></script>

@yield('scripts')

<script type="text/javascript">
$(document).ready(function (){
    $("#loading").fadeOut(500);

    //按钮提交 eg:删除
    $(".ajaxbtnsubmit").on("click",function(){    
        $.ajax({
            type: "get",
            url: $(this).attr('dataurl'),
            data:{'id':$(this).attr('dataid')},
            dataType: "json",
            success: function (da) {
                alert(da.msg);
                if (da.error == 0) {
                    window.location.reload(); 
                }
            },
        });
        return false;            
    }) 

    //表单提交 eg:add edit
    $(".ajaxformsubmit").on("click",function(){    
        $.ajax({
            type: "POST",
            url: $(this).attr('dataurl'),
            data:$(this).parents('form').serialize(),
            dataType: "json",
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            success: function (da) {
                alert(da.msg);
                if (da.error == 0) {
                    window.location.href = da.url; 
                }
            },
        });
        return false;            
    })

    //排序 编辑
    $(".ajaxinputsubmit").on("blur",function(){    
        $.ajax({
            type: "POST",
            url: $(this).attr('dataurl'),
            data:{'id':$(this).attr('dataid'),'val':$(this).val()},
            dataType: "json",
            headers: {'X-CSRF-TOKEN':'{{ csrf_token() }}'},
            success: function (da) {
                alert(da.msg);
                if (da.error == 0) {
                    window.location.reload(); 
                }
            },
        });
        return false;            
    })
    
    
});    
</script>
</body>
</html>
