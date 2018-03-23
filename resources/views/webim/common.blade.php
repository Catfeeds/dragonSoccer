<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="format-detection" content="telephone=no">
    <meta name="viewport" content="initial-scale=1.0, minimum-scale=1.0, maximum-scale=2.0, user-scalable=no, width=device-width">
    <!-- iOS 移动设备添加主屏幕标题设置 1205-->
    <meta name="apple-mobile-web-app-title" content="龙少足球" >
    <title>龙少足球-客服</title>
    <script src="/bower_components/jquery/dist/jquery.min.js"></script>
    <link rel="stylesheet" href="/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/bower_components/font-awesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="/bower_components/select2/select2.min.css">
    <link rel="stylesheet" href="/bower_components/admin-lte/AdminLTE.min.css">
    <link rel="stylesheet" href="/bower_components/admin-lte/_all-skins.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/bower_components/ionicons/ionicons.min.css">
    <link rel="stylesheet" href="/css/error-alert.css">
    @yield('css')
</head>
<body data-spy="scroll"  style="background: #f5f5f5;">
    <div class="container">
        @yield('content')
        <!-- Main Footer -->
        <footer class="row" style="max-height: 100px;text-align: center">
            <strong>Copyright © 2017 <a href="#">龙之少年</a>.</strong> All rights reserved.
        </footer>
    </div>
    <script type='text/javascript' src='/webim/webim.config.js'></script>
    <script type='text/javascript' src='/webim/strophe-1.2.8.min.js'></script>
    <script type='text/javascript' src='/webim/websdk-1.4.13.js'></script>
    @yield('scripts')
</body>
</html>
