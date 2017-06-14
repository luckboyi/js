<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>选服</title>
    <link rel="stylesheet" type="text/css" href="http://pic.vronline.com/common/style/base.css">
    <link rel="stylesheet" href="http://pic.vronline.com/common/style/login.css">
    <script src="http://pic.vronline.com/common/js/jquery-1.12.3.min.js"></script>
    <script src="http://pic.vronline.com/webgame/js/tinyscrollbar.js"></script>
    <script src="http://pic.vronline.com/common/js/login_win.js"></script>
</head>
<body @if (isset($images['bg']) && $images['bg']) style="background:url({{$images['bg']}}) top center;" @endif>
<div class="pageGame_user_msg">
    <span class="fr refresh"><i></i>刷新</span>
    <!-- <span class="fr mute">静音</span> -->
    <!-- <span class="fr change">换服</span> -->
    @if ($islogin)
    <!-- <span class="fr exit">退出</span> -->
    <span class="fr userName"><i></i>{{ $nick ? $nick : $account }}</span>
    @else
    <span class="fr login" onclick="loginFn.login();" style="cursor:pointer;"><i></i>登录</span>
    @endif
</div>
<!--选择服务器S-->
<div class="choose_suit">
        <div class="in_suit pr" id="suit_list" game-id="100001"  user-id="">
            <div class="scrollbar pr fr">
                <div class="track">
                    <div class="thumb pa"></div>
                </div>
            </div>
            <div class="viewport pr">
                <div class="overview">
                    <div class="rec_suit">
                        <div class="title clearfix">
                            <h3 class="fl">推荐服务器</h3>
                            <span class="fl"><i class=""></i>畅通</span>
                            <span class="fl"><i class="status3 "></i>拥挤</span>
                            <span class="fl"><i class="status6"></i>繁忙</span>
                            <span class="fl"><i class="status9"></i>维护</span>
                        </div>
                        <ul class="suit_con clearfix">
                        @foreach($recommend as $info)
                            @if (isset($allservers[$info['serverid']]))
                            <li area-id="10001" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}"><b @if ($allservers[$info['serverid']]['isnew']) class="tips" @endif></b><i class="{{ $allservers[$info['serverid']]['status'] == 0 ? '' : 'status'.$allservers[$info['serverid']]['status'] }}"></i><b>{{ $allservers[$info['serverid']]['name'] }}</b></li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                    <div class="recently_suit">
                        <h3>最近登录服务器</h3>
                        <ul class="suit_con clearfix">
                        @foreach($myservers as $info)
                            @if (isset($allservers[$info['serverid']]))
                            <li area-id="10001" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}"><b @if ($allservers[$info['serverid']]['isnew']) class="tips" @endif></b><i class="{{ $allservers[$info['serverid']]['status'] == 0 ? '' : 'status'.$allservers[$info['serverid']]['status'] }}"></i><b>{{ $allservers[$info['serverid']]['name'] }}</b></li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                    <div class="all_suit">
                        <h3>所有服务器</h3>
                        <ul class="suit_con clearfix">
                        @foreach($allservers as $info)
                            @if (isset($allservers[$info['serverid']]))
                            <li area-id="10001" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}"><b @if ($info['isnew']) class="tips" @endif></b><i class="{{ $info['status'] == 0 ? '' : 'status'.$info['status'] }}"></i><b>{{ $info['name'] }}</b></li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
<!--选择服务器S-->
<script>
    $(function(){
        if (window.CppCall == undefined) {
            window.CppCall = function () {};
        };
        var url = location.href;
        $('#suit_list').tinyscrollbar();
        //点击选服列表修改地址
        $('.in_suit .suit_con').on('click','li',function(){
            window.location.href =$(this).attr('game-src');
        });

        //是否登录
        $('.pageGame_user_msg').find('span').on('click',function(){
            if($(this).hasClass('refresh')){
                window.location.href =location.href;
            }else if($(this).hasClass('mute')){
                $(this).toggleClass('offmute')
            }else if($(this).hasClass('change')){
                window.location.href =url;
            }else if($(this).hasClass('exit')){
                $(this).hide();
                $(this).next('.userName').hide();
                window.CppCall('common', 'close','');
            }
        })
    })
</script>
</body>
</html>
