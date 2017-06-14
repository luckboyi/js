@extends('website.webgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')礼包领取@endsection

@section('webgameRight')
    <!-- BEGIN PAGE -->
    <div class="right_con fr detail_container">
        <div class="viewport pr ">
            <div class="receive_gift">
                <div class="gift_con">
                    <h4>礼包领取</h4>
                    @if(!empty($giftListData) && count($giftListData) > 0 && is_array($giftListData))
                        @foreach($giftListData as $gk=>$gv)
                            <div class="in_gift_con pr">
                                <div class="clearfix">
                                    <span class="fl">所选游戏：</span>
                                    <span class="fl" id="gameName">{{ $gv['gameName'] }}</span>
                                </div>
                                <div class="clearfix nav">
                                    <span class="fl">选择礼包：</span>
                                    {{--<span class="fl">无</span>--}}
                                    <select name="selectSort" class="default" id="selectSort"  onchange="changeValue($('#selectSort').val())">
                                        @if(!empty($selectData) && count($selectData) > 0 && is_array($selectData))
                                            @foreach($selectData as $sk=>$sv)
                                                    <option id="game_{{ $sk }}" value="{{ $sk }}" <?php if ($sv['gid'] == $gv['gid']) {echo "selected";}?> >{{ $sv['name'] }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                                {{--<div class="clearfix">--}}
                                    {{--<span class="fl">领取限制：</span>--}}
                                    {{--<span class="fl">无</span>--}}
                                {{--</div>--}}
                                <div class="clearfix">
                                    <span class="fl">有效时间：</span>
                                    <span class="fl" id="start">{{ $gv['start'] }}</span>
                                    {{--<span class="fl">&nbsp;00:00</span>--}}
                                    <span class="fl" id="end">~{{ $gv['end'] }}</span>
                                    {{--<span class="fl">&nbsp;24:00</span>--}}
                                </div>
                                <div class="clearfix">
                                    <span class="fl">礼包内容：</span>
                                    <div class="fl">
                                        <p><em id="name">{{ $gv['name'] }}</em><b>&nbsp;(剩余</b><i id="codeNum">{{ $gv['codeNum'] }}</i><b>)</b></p>
                                        <p class="gift_detail"><i id="desc">{{ $gv['desc'] }}</i></p>
                                        <p class="gift_detail"><i id="content">{{ $gv['content'] }}</i></p>
                                    </div>
                                </div>
                                <div class="get_gift pa">
                                    <p class="in_game playGame"  style="cursor: pointer" id="key" data-userId="{{ $gv['uid'] }}" data-gameName="{{ $gv['gameName'] }}" data-appId="{{ $appId }}" data-serverId="{{ $gv['serverid'] }}" data-gid="{{ $gv['gid'] }}">立即领取</p>
                                    <p  class="start-web-game" game-id="{{$appId}}" server-id=-1 game-name="{{$gv['gameName']}}">开始游戏</p>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </div>
    <div class="pageGame_container fr">
        <div class="pageGame_right_con pr pageGame_con_hei show"  id="gift_con_scrollbar" >
            <div class="scrollbar fr pa  pageGame_con_hei">
                <div class="track">
                    <div class="thumb pa"></div>
                </div>
            </div>
            <div class="viewport pr pageGame_con_hei">
                <div class="overview">
                    <div class="gift_con">
                        <h4>礼包领取</h4>
                        @if(!empty($giftListData) && count($giftListData) > 0 && is_array($giftListData))
                            @foreach($giftListData as $gk=>$gv)
                                <div class="in_gift_con pr">
                                    <div class="clearfix">
                                        <span class="fl">所选游戏：</span>
                                        <span class="fl" id="gameName">{{ $gv['gameName'] }}</span>
                                    </div>
                                    <div class="clearfix nav">
                                        <span class="fl">选择礼包：</span>
                                        {{--<span class="fl">无</span>--}}
                                        <select name="selectSort" class="default" id="selectSort"  onchange="changeValue($('#selectSort').val())">
                                            @if(!empty($selectData) && count($selectData) > 0 && is_array($selectData))
                                                @foreach($selectData as $sk=>$sv)
                                                    @if($sv['codeNum'] > 0)
                                                        <option id="game_{{ $sk }}" value="{{ $sk }}" <?php if ($sv['gid'] == $gv['gid']) {echo "selected";}?> >{{ $sv['name'] }}</option>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </select>

                                    </div>
                                    <div class="clearfix">
                                        <span class="fl">有效时间：</span>
                                        <span class="fl" id="start">{{ $gv['start'] }}</span>
                                        {{--<span class="fl">&nbsp;00:00</span>--}}
                                        <span class="fl" id="end">~{{ $gv['end'] }}</span>
                                        {{--<span class="fl">&nbsp;24:00</span>--}}
                                    </div>
                                    <div class="clearfix">
                                        <span class="fl">礼包内容：</span>
                                        <div class="fl">
                                            <p><em id="name">{{ $gv['name'] }}</em><b>&nbsp;(剩余</b><i id="codeNum">{{ $gv['codeNum'] }}</i><b>)</b></p>
                                            <p class="gift_detail"><i id="desc">{{ $gv['desc'] }}</i></p>
                                            <p class="gift_detail"><i id="content">{{ $gv['content'] }}</i></p>
                                        </div>
                                    </div>
                                    <div class="get_gift pa">
                                        <p class="in_game" style="cursor: pointer" id="key" data-userId="{{ $gv['uid'] }}" data-gameName="{{ $gv['gameName'] }}" data-appId="{{ $appId }}" data-serverId="{{ $gv['serverid'] }}" data-gid="{{ $gv['gid'] }}">立即领取</p>
                                        <p>开始游戏</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- END PAGE -->
@endsection

@section('javascript-webgame')
    <script src="{{static_res('/common/js/common-my.js')}}"></script>
    {{--<script src="{{static_res('webgame/js/pageGame.js')}}"></script>--}}
    <script src="{{static_res('/website/js/bannerVideo.js')}}"></script>
    <script type="text/javascript">
        $('#videoBanner').movingBoxes({
            startPanel   : 1,
            reducedSize  : .5,
            wrap         : true,
            buildNav     : true
        });
        //根据select选择框更新响应数据
        function changeValue(v) {
            var data = <?php echo $selectJsonData; ?> ;
            console.dir(data);
            //alert(gameName);
            $("#start").text(data[v].start);
            $("#end").text( '~'+ data[v].end);
            $("#name").text(data[v].name);
            $("#codeNum").text(data[v].codeNum);
            $("#desc").text(data[v].desc);
            $("#content").text(data[v].content);
            $("p.in_game").attr("data-userId", data[v].uid );
            $("p.in_game").attr("data-gid", data[v].gid);
            $("p.in_game").attr("data-gameName", data[v].gameName);
            $("p.in_game").attr("data-serverId", data[v].serverid);
        }
        //领取礼包事件
        $("body").on("click", "p.in_game", function() {
            var userId = $(this).attr("data-userId"),
                    gid = $(this).attr("data-gid"),
                    gameName = $(this).attr("data-gameName"),
                    serverId = $(this).attr("data-serverId"),
                    appId = $(this).attr("data-appId");
            if($.trim(userId) == "" ) {
                //window.CppCall('loginframe', 'showlogin', '');
                randomArr.createHtml(0, '领取失败', '请先登录' , 'erro'); //失败调用数据
                return false;
            }
            if ($.trim(userId) == "" || $.trim(gid) == ""){
                //pubApi.showDomError($("#addErrorBox"), "非法参数");
                alert("非法参数！");
                return false;
            }

            var paramObj = {
                "action" : "getCode",
                "gid" : gid,
                "userid" : userId,
                'appname' : gameName,
                'appid' : appId,
                'serverd' : serverId,
            };

            var ajaxUrl = "{{ url('getGiftCode') }}";

            var jumpUrl = '' , url = 'website/getMyPackage';
            if (/^http/i.test(url)) {
                jumpUrl = url;
            } else {
                jumpUrl = 'http://' + window.location.host + '/' + url;
            }
            pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    randomArr.createHtml(1, '领取成功', result.data.vCode, '', jumpUrl);  //领取成功
                }
            }, function(result) {
                if(result.code == 2301) {  //已经领取过，也提示领取成功页面
                    randomArr.createHtml(1, '领取成功', result.data.vCode, '', jumpUrl);  //领取成功
                } else {
                    randomArr.createHtml(0, '领取失败', result.msg , 'erro'); //失败调用数据
                }

                pubApi.showError();
            });
        });
        /*
         **关闭弹窗
         */
        $("body").on("click", "#sureBtn", function() {
            $('.gift_get').removeClass('show');
        });
    </script>
@endsection
