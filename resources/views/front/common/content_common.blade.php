<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, user-scalable=no">
    <title>{{empty($title)?'龙少足球':$title}}</title>
    <!--bootstrap-->
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <script src="/bootstrap/js/bootstrap.min.js"></script>
    @yield('css') 
    <style type="text/css">
	p{ word-break:break-all;overflow:auto;text-indent:2px;}	
	</style>   
</head>

<body>
<div class="container">
    @yield('content')
</div>
    
</body>
@yield('scripts')
</html>