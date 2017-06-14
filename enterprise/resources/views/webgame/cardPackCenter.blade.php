<?php
/**
 * Created by PhpStorm.
 * User: libb
 * Date: 2016/9/5
 * Time: 17:52
 */
?>
@extends('layouts.webgame')

@section('title', '卡包中心')

{{--@include('common.errors')--}}
@section('content')
    <!-- BEGIN PAGE -->
    <div class="pageGame_container">
        <div class="pageGame_right_con pr pageGame_con_hei show"  id="pageGame_con_scrollbar" >
            <div class="scrollbar fr pa  pageGame_con_hei">
                <div class="track">
                    <div class="thumb pa"></div>
                </div>
            </div>
            <div class="viewport pr pageGame_con_hei">
                <div class="overview">

                    <div class="pageGame_list_item">

                        <div class="list_item">
                            <div class="item_header clearfix">
                                <h4 class="fl">游戏礼包</h4>
                                <p class="fr">
                                    @if($islogin)
                                        <a href="{{ url('getMyPackage') }}">我的礼包</a>
                                    @endif
                                </p>
                            </div>
                            <div class="list_title clearfix">
                                {{--<span class="fl cur"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                                {{--<span class="fl"><a href="javascript:;">全部</a></span>--}}
                            </div>
                            <div class="hot_list gift_center" style="margin-top:10px;">
                                <ul class="clearfix">

                                    @if(!empty($giftListData) && count($giftListData) > 0)
                                        @foreach($giftListData as $gk=>$gv)
                                            <li class="fl">
                                                <a href="javascript:;" class="pr">
                                                    <img src="{{ url('images/list.jpg') }}">
                                                    <i class="new"></i>
                                                    <p class="pa title"><b class="fl">{{ $gv['gameName'] }}</b><i class="fl">{{  $gv['name']  }}</i></p>

                                                    {{--@if($gv['codeNum'] === 0 || $gv['codeNum'] == '')--}}
                                                        {{--<div class="has_had pa"><span>已领完</span></div>--}}
                                                    {{--@else--}}
                                                        <div class="btn pa">
                                                            <p class="in_game"  id="key" data-appId="{{ $gv['appid'] }}" data-gid="{{ $gv['gid'] }}">领取礼包</p>
                                                        </div>
                                                    {{--@endif--}}
                                                </a>
                                            </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    {{--</div>--}}
    <!-- END PAGE -->
@endsection

@section('javascript')
    <script type="text/javascript">

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
            var url = 'packageReceive/' + appId;
            pubApi.jumpUrl(url);
        });

    </script>
@endsection