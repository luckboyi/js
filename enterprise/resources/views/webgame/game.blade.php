@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{ $servername }}</title>
    <link rel="stylesheet" type="text/css" href="{{static_res('/common/style/base.css')}}">
    <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
    <script src="{{static_res('/common/js/datacenter_stat.js')}}"></script>
    <style>
        .game_frame{
            width: 100%;
            height: 100%;
            position: relative;
        }
    </style>
    <script language="javascript">
        var kingnetObj = {advid:window.adid,uid:{{$uid}},sid:7,op:8};
    </script>
    <script src='//static.kingnetdc.com/kingnet.js' type='text/javascript'></script>
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
    <div class="pr game_frame_con" style="z-index:8">
        <iframe src="<?=$gameurl?>" frameborder="0" width="100%" height="100%"></iframe>
    </div>

</div>
</body>
</html>
<script>
    $(function(){
        kad.customEvent('playing',{
            appid:{{$appid}},
            gameurl:"<?=$gameurl?>"
        });

        //获取窗口的高度
        getWindowSize();
        $(window).resize(function(){
            getWindowSize();
        })
        function getWindowSize(){
            var winHeight,winWidth;
            if (window.innerHeight){
                winHeight = window.innerHeight;
            }else if ((document.body) && (document.body.clientHeight))
            { winHeight = document.body.clientHeight;}
            if (window.innerWidth){
                winWidth = window.innerWidth;
            }else if ((document.body) && (document.body.clientWidth))
            { winWidth = document.body.clientWidth;}

            $('.game_frame_con').height(winHeight-38).width(winWidth-10);
        }

        //点击关闭充值页面
        $('.charge .close_btn').on('click',function(){
            $('.charge_frame').hide();
        });
        //点击充值按钮打开

        /*
         * 统计
         */
        $(".fr.refresh").on("click", function(){
            var props = {"catalog":"click", "actid":"click_webgame_game_refresh_button"};
            dcstat.send("pageclick", props);
            window.location.reload();
        });
    })
</script>
