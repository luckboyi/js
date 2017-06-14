<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $servername }}</title>
    <link rel="stylesheet" type="text/css" href="http://pic.vronline.com/common/style/base.css">
    <style>
        .game_frame{
            width: 100%;
            height: 100%;
            position: relative;
            text-align:center;
            font-size:20px;
        }
    </style>
</head>
<body>
<div class="pageGame_user_msg">
    <span class="fr refresh"><i></i>刷新</span>
    <!-- <span class="fr mute">静音</span> -->
    <span class="fr change" onclick="window.location.href='http://webgame.vronline.com/servers/{{$appid}}'"><i></i>换服</span>
    @if ($islogin)
    <!-- <span class="fr exit">退出</span> -->
    <span class="fr userName"><i></i>{{ $nick ? $nick : $account }}</span>
    @endif
</div>
<div class="game_frame">
        <br><br>{{$msg}}
</div>
</body>
</html>