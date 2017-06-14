<?php
/**
 * Created by PhpStorm.
 * User: Kira
 * Date: 2016/9/14
 * Time: 15:46
 */
;?>
@inject('blade', 'App\Helper\BladeHelper')
<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- Required meta tags always come first -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <!--页面的Title--->
        <title>@yield('title')</title>

        <!-- 引入 CSS -->
        <link href="{{static_res('/common/style/base.css')}}" rel="stylesheet">
        <script src="{{static_res('/common/js/jquery-1.12.3.min.js')}}"></script>
        <script src="{{static_res('/common/js/ClientConfig.js')}}"></script>
        <script src="{{static_res('/webgame/js/tinyscrollbar.js')}}"></script>
        <script src="{{static_res('/webgame/js/pageGame.js')}}"></script>
        <!--common script for all pages-->
        <script src="{{static_res('/common/js/common-my.js')}}"></script>
        <script src="{{static_res('/assets/artTemplate/template.js')}}"></script>
    </head>
    <body>
        <!--添加相关的body-->
        <div class="pageGame_container" id="pageGame_container">
            <div class="pageGame_con pr fl pageGame_con_hei" id="pageGame_left_list_scrollbar">
                <div class="scrollbar fr pa  pageGame_con_hei">
                    <div class="track">
                        <div class="thumb pa"></div>
                    </div>
                </div>
                <div class="viewport pr pageGame_con_hei">
                    <div class="overview">
                        <h3>最近游戏</h3>
                        <div id="pageGame_scrollbar" class="list_con pr">

                            <div class="viewport pr">
                                <div class="overview">
                                    @if(isset($history_logs) && count($history_logs)>0)
                                    <ul>
                                        @foreach ($history_logs as $history)
                                        @if(isset($servers[$history["appid"]."_".$history["serverid"]]))
                                        <li class="startGame" game-id="{{$history["appid"]}}" area-id="{{$history["serverid"]}}" area-name="{{ $servers[$history["appid"]."_".$history["serverid"]]["name"] }}">
                                            <a href="javascript:;" class="clearfix">
                                                <img src="{{ $webgames[$history["appid"]]["img_url"] }}" class="fl">
                                                <div class="in_list_con fl">
                                                    <p>{{ $webgames[$history["appid"]]["name"] }}</p>
                                                    <span>{{ $servers[$history["appid"]."_".$history["serverid"]]["name"] }}</span>
                                                </div>
                                            </a>
                                        </li>
                                        @endif
                                        @endforeach
                                    </ul>
                                    @else
                                    <div class="noGame_history">
                                        <span></span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="tab">
                            <ul class="clearfix f14" style="height: 30px;">
                                <li class="fl cur">
                                    <a href="javascript:;">新服预告</a>
                                </li>
                                <li class="fl">
                                    <a href="javascript:;">已开新服</a>
                                </li>
                            </ul>
                            <ol style="display: table;">
                                <li class="cur">
                                    <table>
                                        <tr>
                                            <th>游戏名称</th>
                                            <th>服务器</th>
                                            <th>时间</th>
                                        </tr>
                                        @if(isset($preServers) && count($preServers)>0)
                                        @foreach ($preServers as $server)
                                        <tr class="gameList{{ isset($prefirst)?"":" cur" }} startGame" game-id="{{$server["appid"]}}" area-id="{{$server["serverid"]}}" area-name="{{ $server["name"] }}">
                                            <td>
                                                @if(isset($webgames[$server["appid"]]))
                                                {{ $webgames[$server["appid"]]["name"] }}
                                                @else
                                                未知游戏
                                                @endif
                                            </td>
                                            <td>{{ $server["name"] }}</td>
                                            <td class="pr">
                                                <div class="clearfix">
                                                    <i class="fl">{{ date("m-d",$server["start"]) }}</i><b class="fl">{{ date("H:i",$server["start"]) }}</b>
                                                </div>
                                                <div class="pa play_game">进入游戏</div>
                                            </td>
                                        </tr>
                                        <?php $prefirst = 1;?>
                                        @endforeach
                                        @else
                                        <tr class="gameList">
                                            <td>暂无新服</td>
                                            <td>暂无新服</td>
                                            <td>暂无新服</td>
                                        </tr>
                                        @endif
                                    </table>
                                </li>
                                <li>
                                    <table>
                                        <tr>
                                            <th>游戏名称</th>
                                            <th>服务器</th>
                                            <th>时间</th>
                                        </tr>
                                        @if(isset($newServers) && count($newServers)>0)
                                        @foreach ($newServers as $server)
                                        <tr class="gameList{{ isset($newfirst)?"":" cur" }} startGame" game-id="{{$server["appid"]}}" area-id="{{$server["serverid"]}}" area-name="{{ $server["name"] }}" >
                                            <td>
                                                @if(isset($webgames[$server["appid"]]))
                                                {{ $webgames[$server["appid"]]["name"] }}
                                                @else
                                                未知游戏
                                                @endif
                                            </td>
                                            <td>{{ $server["name"] }}</td>
                                            <td class="pr">
                                                <div class="clearfix">
                                                    <i class="fl">{{ date("m-d",$server["start"]) }}</i><b class="fl">{{ date("H:i",$server["start"]) }}</b>
                                                </div>
                                                <div class="pa play_game">进入游戏</div>
                                            </td>
                                        </tr>
                                        <?php $newfirst = 1;?>
                                        @endforeach
                                        @else
                                        <tr class="gameList">
                                            <td>暂无新服</td>
                                            <td>暂无新服</td>
                                            <td>暂无新服</td>
                                        </tr>
                                        @endif
                                    </table>
                                </li>
                            </ol>
                        </div>
                        <div class="ad">
                            <ul>
                                @foreach ($sideAds as $ad)
                                <li>
                                    <a href="{{ url(explode("com/", $ad->content_url)[1]) }}">
                                        <img src="{{ $ad->resource }}">
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div id="mainShowCon">
                @yield('content')
            </div>
        </div>
    </body>
</html>
<!--最后添加的js代码-->
@yield('javascript')

<script id="detailTemp" type="text/html">
<div class="pageGame_right_con pr pageGame_con_hei pageGame_detail "  id="pageGame_con_scrollbar" >
    <div class="scrollbar fr pa  pageGame_con_hei">
        <div class="track">
            <div class="thumb pa"></div>
        </div>
    </div>
    <div class="viewport pr pageGame_con_hei">
        <ul class="overview">
            <li class="detail_con cur" game-id="@{appid}@">
                <div class="detail clearfix">
                    <div class="left_contain fl">
                        <h3>@{name}@</h3>
                        <div class="lanrenzhijia">
                            <div id="picBox" class="picBox">
                                <ul class="cf">
                                    @{each img_list}@
                                    <li><img src="@{$value}@" />{{-- <i class="btn-link-i"> --}}</a></li>
                                    @{/each}@
                                </ul>
                            </div>
                            <div id="listBox" class="listBox">
                                <ul class="cf">
                                    @{each img_list}@
                                    <li @{if $index==0}@ class="on" @{/if}@><div class="arrow-up pa"></div><img src="@{$value}@" />{{-- <i class="btn-link-i"></i> --}}</li>
                                    @{/each}@
                                </ul>
                            </div>
                            <div class="detail_btn">
                                <span id="prev" class="btn prev"></span>
                                <span id="next" class="btn next"></span>
                                <span class="btn_center" id="center"><em></em></span>
                            </div>
                        </div>
                    </div>
                    <div class="right_contain fl">
                        <div class="get_gift">
                            <p class="pr getGift@{if hasgift==0}@ hasget@{/if}@" appid="@{appid}@">领取礼包</p>
                            <div class="clearfix">
                                <span class="fl" onclick="window.location.href='http://www.vronline.com/pay'" act="pay">账号充值</span>
                                <span class="fl" onclick="window.open('http://bbs.vronline.com/')" act="bbs">进入论坛</span>
                            </div>
                        </div>
                        <div class="content1">
                            <div class="right_con">
                                <h3>游戏评分</h3>
                            </div>
                            <div class="score clearfix">
                                <span class="fl" id="score">@{score}@</span><i class="fl">分</i><p class="fl">（已有</p><b class="fl" id="comment-num">@{score_num}@</b><p class="fl">人评分）</p>
                            </div>
                        </div>
                        <div class="content2">
                            <div class="right_con clearfix">
                                <h3 class="fl" style="margin-right: 28px">游戏类型</h3><h3 class="fl">上市时间</h3>
                            </div>
                            <ul class="con_list clearfix">
                                <li><a href="javascript:;">@{first_class}@</a></li>
                                <li><a href="javascript:;">@{send_time}@</a></li>
                            </ul>
                        </div>
                        <div class="content1">
                            <div class="right_con">
                                <h3>游戏简介</h3>
                            </div>
                            <p>@{desc}@</p>
                        </div>
                    </div>
                </div>
                <div class="detail_comment">
                    <div class="comment ">
                        <ul class="comment_head clearfix">
                            <li class="fl cur language comment-tab" comment-type="hot">精彩评论</li>
                            <li class="fl language comment-tab" comment-type="new">最新评论</li>
                            @{if isLogin==1}@
                            <li class="fl myComment language comment-tab" comment-type="my">我的评论</li>
                            @{/if}@
                        </ul>
                        <div class="comment_body">
                            <ol class="in_comment_body" id="comment_con">

                            </ol>
                            <div class="comment_page" id="page"></div>
                        </div>
                        <div class="comment_foot">
                            <p class="have_comment clearfix"><i class="fl language" data-name="has">已经有</i><b class="fl">@{allCommentNum}@</b><i class="fl language" data-name="evaluating">条评论</i></p>
                            @{if isLogin!=1}@
                            <div class="add_comment pr">
                                <textarea  cols="30" rows="10" data-name="comment_add" id="content"></textarea>
                                <div class="comment_login" style="display: block;">
                                    <div class="in_com_login clearfix">
                                        <p class="un_login fl language" data-name="un_login">未登录，请</p>
                                        <p class="fl login_in_text language" data-name="login_enter">登录</p>
                                        <p class="fl language" data-name="or">或</p>
                                        <p class="fl login_text language" data-name="register">注册</p>
                                    </div>
                                </div>
                            </div>
                            @{else if comment!=1}@
                            <div class="add_comment pr no-comment">
                                <textarea  cols="30" rows="10" data-name="comment_add" id="content"></textarea>
                            </div>
                            <div class="in_comment_foot clearfix no-comment">
                                <div class="fl comment_grade pr" id="gradeCon">
                                    <ul class="un_sel fl">
                                        <li></li>
                                    </ul>
                                    <span class="grade_mouse pa grade"></span>
                                    <span class="game_score clearfix score"><b class="fl" score="@{score}@">@{score}@</b><em class="fl language">分</em></span>
                                </div>
                                <div class="fr clearfix comment_btn">
                                    <p class="fl language" data-name="like_game"></p>
                                    <ul class="fl clearfix">
                                        <li class="fl cur language isGood" is-good="1">喜欢</li>
                                        <li  class="fl language isGood" is-good="0">不喜欢</li>
                                    </ul>
                                    <p class="fl send language" data-name="send" id="sendComment">发送</p>
                                </div>
                            </div>
                            <div class="has-comment" style="display: none">您已经评论过了</div>
                            @{else}@
                            <div class="has-comment">您已经评论过了</div>
                            @{/if}@
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="language start_btn" data-name="start" game-id="@{appid}@" game-name="@{name}@">开始游戏</div>
    <input type="hidden" id="target-id" value="@{appid}@">
    <input type="hidden" id="target-type" value="1" >
</div>
</script>

<script src="{{static_res('/common/js/tips.js')}}"></script>
<script src="{{static_res('/common/js/pagination.js')}}"></script>
<script src="{{static_res('/common/js/comment.js')}}"></script>
<script src="{{asset('js/webGameDetail.js')}}"></script>
<script src="{{static_res('/common/js/datacenter_stat.js')}}"></script>