<html lang="en"><head>
    <meta charset="UTF-8">
    <title></title>
    <link rel="stylesheet" href="{{ static_res('common/style/base.css') }}">
    <link rel="stylesheet" href="{{ static_res('/common/style/login.css') }}">
    <script language="JavaScript" src="{{ static_res('/common/js/jquery-1.12.3.min.js') }}"></script>
    <script language="JavaScript" src="{{ static_res('/common/js/login_win.js') }}"></script>
</head>
<body>

</body>
</html>
<script>
    $(function(){
         if (window.CppCall == undefined) {
            loginFn.login();
        }else {
            window.CppCall('loginframe', 'showlogin', '');
            return false;
        }
    })
    //登录
</script>
