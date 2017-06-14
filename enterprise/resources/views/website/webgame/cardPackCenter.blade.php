@extends('website.webgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')卡包中心@endsection

@section('webgameRight')
    <!-- BEGIN PAGE -->

    <div class="right_con fr detail_container">
        <div class="pageGame_list_item">
            <div class="list_item">
                <div class="item_header clearfix">
                    <h4 class="fl">游戏礼包</h4>
                    <p class="fr">@if($islogin) <a href="{{ url('getMyPackage') }}">我的礼包</a> @endif </p>
                </div>
                <br/>
                <div class="list_title clearfix">
                    {{--<span class="fl cur"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                    {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                </div>
                <div class="hot_list gift_center">
                    <ul class="clearfix">
                        @if(!empty($giftListData) && is_array($giftListData) && count($giftListData) > 0)
                            @foreach($giftListData as $gk=>$gv)
                                <li class="fl">
                                    <a href="javascript:;" class="pr">
                                        <img src="{{ static_image($gv["image"]["logo"],226) }}">
                                        <p class="pa title"><b class="fl">{{ $gv['gameName'] }}</b><i class="fl">{{  $gv['name']  }}</i></p>

                                        {{--@if($gv['codeNum'] === 0 || $gv['codeNum'] == '')--}}
                                        {{--<div class="has_had pa"><span>已领完</span></div>--}}
                                        {{--@else--}}
                                        <div class="btn pa">
                                            <p class="in_game"  id="key" data-appId="{{ $gv['appid'] }}" data-gid="{{ $gv['gid'] }}">领取礼包</p>
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
    </div>
<!-- END PAGE -->
@endsection

@section('javascript-webgame')
    <script src="{{static_res('/common/js/common-my.js')}}"></script>
    <script src="{{static_res('/website/js/bannerVideo.js')}}"></script>
    <script type="text/javascript">
        $('#videoBanner').movingBoxes({
            startPanel   : 1,
            reducedSize  : .5,
            wrap         : true,
            buildNav     : true
        });



        $("body").on("click", "p.in_game", function() {
            var gid = $(this).attr("data-gid"),
                    appId = $(this).attr("data-appId");
            if ($.trim(appId) == ""){
                //pubApi.showDomError($("#addErrorBox"), "非法参数");
                alert("非法参数！");
                return false;
            }

            var paramObj = {
                "action" : "getCode",
                "gid" : gid,
            };
            var url = 'website/packageReceive/' + appId;
            pubApi.jumpUrl(url);
        });

    </script>
@endsection
