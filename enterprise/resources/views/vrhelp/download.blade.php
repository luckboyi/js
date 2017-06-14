@extends('vrhelp.layout')
@section('meta')
<title>游戏库</title>
@endsection

@section('head')
<style type="text/css">
    .choosen{
        height:25px;
        overflow:hidden;
    }
    .paging{float:right;}
    .paging li{float:left;color:#fff;margin-left: 5px;cursor:pointer;}
</style>
@endsection


@section('content')
        <div class="main_con">
            <div class="download_con">
                <div class="download_nav tac">
                    <ul class="clearfix">
                        <li class="fl cur f16 cp">已下载（<b>0</b>）</li>
                        <li class="fl f16 cp">正在下载<b>0</b>）</li>
                    </ul>
                </div>
                <div class="download_body f16">
                    <ul class="clearfix">
                        <li class="fl  pr cp"><i class="icon play_grey_icon pa"></i>继续所有任务</li>
                        <li class="fl">|</li>
                        <li class="fl  pr cp"><i class="icon delete_grey_icon pa"></i>删除所选任务</li>
                    </ul>
                    <div class="in_download_body">
                        <ol class="cur">
                            <li class="list_head">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">任务名称</div>
                            <div class="fl">大小</div>
                            <div class="fl">状态</div>
                            <div class="fl">操作</div>
                            </li>
                            <!-- <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">vr女友</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">
                                <div class="new_version">
                                        <i class="icon caution_icon"></i>
                                        发现新版本！
                                </div>
                            </div>
                            <div class="fl">
                                <div class="handle_icon update_icon_con pr cp">
                                    <i class="icon pa updatedown_icon"></i>
                                </div>
                            </div>
                            </li>
                            <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">vr女友</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">
                                <div class="success_con ">
                                        <i class="icon success_icon"></i>
                                        更新成功！
                                </div>
                            </div>
                            <div class="fl">
                                    <div class="handle_icon  pr">
                                    <i class="icon pa closex_icon"></i>
                                </div>
                            </div>
                            </li>
                            <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">vr女友</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">
                                <div class="updown_pro_con ">
                                    <p class="updown_pro pr">
                                        <i class="in_pro pa"></i>
                                        <b class="pa">30%</b>
                                    </p>
                                    <p class="f14">正在更新</p>
                                </div>
                            </div>
                            <div class="fl">
                                    <div class="handle_icon  pr cp">
                                    <i class="icon pa pausex_icon"></i>
                                </div>
                            </div>
                            </li>
                            <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">vr女友</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">
                                <div class="fail_con">
                                        <i class="icon fail_icon"></i>
                                        更新失败,请点击右侧“更新“安钮再试一下
                                </div>
                            </div>
                            <div class="fl">
                                    <div class="handle_icon  pr">
                                    <i class="icon pa updatedown_icon"></i>
                                </div>
                            </div>
                            </li>
                            <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">vr女友</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">

                            </div>
                            <div class="fl">
                                    <div class="handle_icon  pr ">
                                    <i class="icon pa playx_icon"></i>
                                </div>
                                <div class="handle_icon  pr ">
                                    <i class="icon pa closex_icon"></i>
                                </div>
                            </div>
                            </li> -->
                        </ol>
                        <ol class="">
                            <li class="list_head">
                                <div class="pr fl"><i class="icon select_icon pa"></i></div>
                                <div class="fl">任务名称</div>
                                <div class="fl">大小</div>
                                <div class="fl">状态</div>
                                <div class="fl">操作</div>
                            </li>
                            @foreach($history as $game)
                            <li class="list_body">
                            <div class="pr fl"><i class="icon select_icon pa"></i></div>
                            <div class="fl">{{ $game['name']}}</div>
                            <div class="fl">32.2g</div>
                            <div class="fl">
                                <div class="updown_pro_con ">
                                    <p class="updown_pro pr">
                                        <i class="in_pro pa" style="width: 0%"></i>
                                        <b class="pa">0%</b>
                                    </p>
                                    <p class="f14">正在下载</p>
                                </div>
                            </div>
                            <div class="fl">
                                    <div class="handle_icon  pr cp">
                                    <i class="icon pa pausex_icon"></i>
                                </div>
                            </div>
                            </li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('javascript')
<script type="text/javascript">
$(function(){
    $('.download_nav li').click(function() {
        $(this).addClass('cur').siblings().removeClass('cur');
        var i = $(this).index();
        $('.in_download_body').find('ol').eq(i).addClass('cur').siblings().removeClass('cur');
    })
})
    $('body').css('background','rgba(255, 255, 255, 0)');
</script>
@endsection
