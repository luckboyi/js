<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 17:52
 */
?>
@extends('layouts.webgame')

@section('title', '礼包领取')

{{--@include('common.errors')--}}
@section('content')
    <!-- BEGIN PAGE -->
    <div class="pageGame_container">
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
                        @if(!empty($giftListData) && count($giftListData) > 0)
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
                                            @if(!empty($selectData) && count($selectData) > 0)
                                                @foreach($selectData as $sk=>$sv)
                                                    @if($sv['codeNum'] > 0)
                                                        <option id="game_{{ $sk }}" value="{{ $sk }}" <?php if($sv['gid'] == $gv['gid']) {echo "selected";}?> >{{ $sv['name'] }}</option>
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
    {{--</div>--}}

    <!-- END PAGE -->
@endsection

@section('javascript')
    <script type="text/javascript">
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
                window.CppCall('loginframe', 'showlogin', '');
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

            var jumpUrl = '' , url = 'getMyPackage/';
            if (/^http/i.test(url)) {
                jumpUrl = url;
            } else {
                jumpUrl = 'http://' + window.location.host + '/' + url;
            }
            pubApi.ajaxPost(ajaxUrl, paramObj, function(result) {
                if(result.code == 0){
                    createHtml(1, '领取成功', result.data.vCode, '', jumpUrl);  //领取成功
                }
            }, function(result) {
                if(result.code == 2301) {  //已经领取过，也提示领取成功页面
                    createHtml(1, '领取成功', result.data.vCode, '', jumpUrl);  //领取成功
                } else {
                    createHtml(0, '领取失败', result.msg , 'erro'); //失败调用数据
                }

                pubApi.showError();
            });
        });
    </script>
@endsection