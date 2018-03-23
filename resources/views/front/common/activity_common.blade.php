<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

    <title>{{empty($title)?'活动-龙少足球':$title}}</title>
    <!--bootstrap-->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>

    <!--Amaze-->
    <link href="/css/amazeui.css" rel="stylesheet">
    <link href="/css/amazeui.min.css" rel="stylesheet">
    @yield('css')
    <style type="text/css">
        /* 页面报错弹窗 */
        .error-modal{
          width: 200px;
          height: 100px;
          padding-top: 35px;
          border-radius: 4px;
          overflow: hidden;
          background-color: rgba(0,0,0,.7);
          position: fixed;
          left: 0;
          right: 0;
          top: 0;
          bottom: 0;
          margin: auto;
          text-align: center;
          display: none;
        }
        .error-modal p{
          width: 100%;
          padding: 0 10px;
          height: 30px;
          line-height: 30px;
          color: #fff;
          font-size: 14px;
          text-align: center;
          letter-spacing: 1px;
        }
    </style>
</head>

<body>

    @yield('content')

<!-- 页面报错弹窗 -->
<div class="error-modal" id="error-modal" style="z-index: 99999999;">
    <p class="ajaxError"></p>
</div>

</body>
<script type="text/javascript">
    //弹出框
    function sendError(msg){
        $('.ajaxError').html(msg);
        $(".error-modal").fadeIn(400,function(){
            $(".error-modal").delay(1000).fadeOut(400);
        });
    }
</script>
<!--Amaze-->
<script src="/js/amazeui.js"></script>
@yield('scripts')


</html>