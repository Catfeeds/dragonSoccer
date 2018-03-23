<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">

    <title>模板文件</title>
    <!--bootstrap-->
    <link href="http://libs.baidu.com/bootstrap/3.0.3/css/bootstrap.min.css" rel="stylesheet">

    <!--Amaze-->
    <link href="http://cdn.amazeui.org/amazeui/2.7.2/css/amazeui.css" rel="stylesheet">

    <link href="http://cdn.amazeui.org/amazeui/2.7.2/css/amazeui.min.css" rel="stylesheet">

    @yield('css')
</head>

<body>

    @yield('content')

</body>

    <script src="http://libs.baidu.com/jquery/2.0.0/jquery.min.js"></script>

    <script src="http://libs.baidu.com/bootstrap/3.0.3/js/bootstrap.min.js"></script>
    <!--Amaze-->
    <script src="http://cdn.amazeui.org/amazeui/2.7.2/js/amazeui.js"></script>

    @yield('scripts')

</html>