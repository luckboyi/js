@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$gameinfo['name']}}</title>
    <link rel="stylesheet" href="{{static_res('/common/style/base.css')}}">
    <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
    <script src="{{static_res('/website/js/tinyscrollbar.js')}}"></script>
    <script language="JavaScript" src="{{ static_res('/common/js/tips.js') }}"></script>
    <style>

    </style>
</head>
<body  class="platform" @if (isset($images['bg']) && $images['bg']) style="background:url({{static_image($images['bg'],100)}}) top center;" @endif>

<div class="pageGame_user_msg">
    <span class="fr refresh"><i></i>刷新</span>
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
                             <h3 class="recommend fl">推荐服务器</h3>
                             <div class="re_content">
                             @if($lastserverid && isset($allservers[$lastserverid]['name']))
                             <a href="javascript:;" area-id="{{$lastserverid}}" isdown="{{$allservers[$lastserverid]['status']}}" game-src="/start/{{ $appid }}/{{$lastserverid}}?gamename={{ rawurlencode($allservers[$lastserverid]['name']) }}">
                             <div class="fl button">
                                <p>{{$allservers[$lastserverid]['name']}}</p>
                                <p>进入游戏</p>
                             </div></a>
                             @endif
                             <div class="advise">
                                 <p><b>我们根据您使用的网络环境为您推荐左侧服务器</b></p>
                                 <span class="fl"><i class=""></i>畅通</span>
                                 <span class="fl"><i class="status3 "></i>拥挤</span>
                                 <span class="fl"><i class="status6"></i>繁忙</span>
                                 <span class="fl"><i class="status9"></i>维护</span>
                             </div>
                             <div class="clearfix"></div>
                              <ul class="suit_con clearfix">
                            <?php $counter = 0;?>
                            @foreach($recommend as $info)
                                @if (isset($allservers[$info['serverid']]) && $info['serverid'] != $lastserverid && $counter < 4)
                                <li area-id="{{ $info['serverid'] }}" isdown="{{$allservers[$info['serverid']]['status']}}" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}"  title="{{ $allservers[$info['serverid']]['name'] }}"><i class="{{ $allservers[$info['serverid']]['status'] == 0 ? '' : 'status'.$allservers[$info['serverid']]['status'] }}"></i><b>{{ $allservers[$info['serverid']]['name'] }}</b>
                                    @if($info['isnew'])
                                    <div class="icon_con">
                                        <span class="new_icon">新</span>
                                    </div>
                                    @endif
                                    <?php $counter++;?>
                                </li>
                                @endif
                            @endforeach
                                </ul>
                            </div>
                         </div>
                    </div>
                    <div class="recently_suit">
                        <h3>最近登录服务器</h3>
                        <ul class="suit_con clearfix">
                        @foreach($myservers as $info)
                            @if (isset($allservers[$info['serverid']]))
                            <li area-id="{{ $info['serverid'] }}" isdown="{{$allservers[$info['serverid']]['status']}}" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}" title="{{ $allservers[$info['serverid']]['name'] }}"><i class="{{ $allservers[$info['serverid']]['status'] == 0 ? '' : 'status'.$allservers[$info['serverid']]['status'] }}"></i><b>{{ $allservers[$info['serverid']]['name'] }}</b>
                                @if ($allservers[$info['serverid']]['isnew'] || $allservers[$info['serverid']]['recommend'])
                                <div class="icon_con">
                                    @if($allservers[$info['serverid']]['recommend'])<span class="rec_icon">荐</span>@endif
                                    @if($allservers[$info['serverid']]['isnew'])<span class="new_icon">新</span>@endif
                                </div>
                                @endif
                            </li>
                            @endif
                        @endforeach
                        </ul>
                    </div>
                    <div class="all_suit">
                        <h3>所有服务器</h3>
                        <ul class="suit_con clearfix">
                        @foreach($allservers as $info)
                            @if (isset($allservers[$info['serverid']]))
                            <li area-id="{{ $info['serverid'] }}" isdown="{{$info['status']}}" game-src="/start/{{ $appid }}/{{ $info['serverid'] }}?gamename={{ rawurlencode($gameinfo['name']) }}" title="{{ $info['name'] }}"><i class="{{ $info['status'] == 0 ? '' : 'status'.$info['status'] }}"></i><b>{{ $info['name'] }}</b>
                                @if ($info['isnew'] || $info['recommend'])
                                <div class="icon_con">
                                    @if($info['recommend'])<span class="rec_icon">荐</span>@endif
                                    @if($info['isnew'])<span class="new_icon">新</span>@endif
                                </div>
                                @endif
                            </li>
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
            var status = $(this).attr("isdown");
            if (status == 9) {
                var config = {
                    headerMsg: "",
                    msg:"服务器正在维护",
                    model: "tips"
                }
                tipsFn.init(config);
                return ;
            }
            window.location.href =$(this).attr('game-src');
        });
        $('.re_content').on('click','a',function(){
            var status = $(this).attr("isdown");
            if (status == 9) {
                var config = {
                    headerMsg: "",
                    msg:"服务器正在维护",
                    model: "tips"
                }
                tipsFn.init(config);
                return ;
            }
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