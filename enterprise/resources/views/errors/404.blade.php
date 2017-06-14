@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>404页面</title>
    <link href="{{static_res('/common/style/base.css')}}" rel="stylesheet">
</head>
<body>
    <div class="error">
        <p>哎呀，出错啦！ 该页面迷失在茫茫宇宙中…</p>
        <div style="text-align: center;">
            <a class="back" href="{{route("home")}}">回到首页</a>
        </div>
        <input type="hidden" name="code" value="{{isset($code)?:""}}">
        <input type="hidden" name="msg" value="{{isset($msg)?:""}}">
    </div>
</body>
</html>
