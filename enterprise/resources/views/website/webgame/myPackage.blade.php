@extends('website.webgame.layout')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')页游@endsection

@section('webgameRight')
    <!-- BEGIN PAGE -->
    <div class="pageGame_container">
        <div class="right_con fr detail_container">
            <div class="viewport pr ">
                <div class="receive_gift">
                    <div class="gift_con">
                        <h4>我的卡包</h4>
                        <div class="in_gift_con pr my_gift_list">
                            <table id="dataList">
                                @if(empty($data))
                                    <h2>你还没有领取礼包记录！</h2>
                                @endif
                            </table>
                            <div class="page" id="pageBtn">
                                {{--<ul class="clearfix">--}}
                                    {{--<li class="fl firstPage">上一页</li>--}}
                                    {{--<li class="fl cur">1</li>--}}
                                    {{--<li class="fl">2</li>--}}
                                    {{--<li class="fl">...</li>--}}
                                    {{--<li class="fl">10</li>--}}
                                    {{--<li class="fl nextPage">下一页</li>--}}
                                {{--</ul>--}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- END PAGE -->
@endsection

@section('javascript-webgame')
    <script language="JavaScript" src="{{static_res('/common/js/tips.js')}}"></script>
    <script src="{{static_res('/website/js/bannerVideo.js')}}"></script>
    <script>
        $('#videoBanner').movingBoxes({
            startPanel   : 1,
            reducedSize  : .5,
            wrap         : true,
            buildNav     : true
        });
        //我的卡包
        $('.my_gift_list').delegate("i.copyBtn","click",function(){
            $(this).parent('td.key').find('input').select();
            document.execCommand('Copy');
            var config = {
                headerMsg: "提示",
                msg: "复制成功",
                model: "tips"
            }

            tipsFn.init(config);
        });

        demo();
        //以下将以jquery.ajax为例，演示一个异步分页
        function demo(curr){
            $.getJSON("{{ $getPageDataUrl }}", {
                page: curr || 1 //向服务端传的参数，此处只是演示
            }, function(res){

                //此处仅仅是为了演示变化的内容*/
                creatHtml(res);
                //var demoContent = (new Date().getTime()/Math.random()/1000)|0;
                //显示分页
                laypage({
                    cont: 'pageBtn', //容器。值支持id名、原生dom对象，jquery对象。【如该容器为】：<div id="page1"></div>
                    pages: res.pages, //通过后台拿到的总页数
                    skin: 'flow', //加载内置皮肤，也可以直接赋值16进制颜色值，如:#c00
                    curr: curr || 1, //当前页
                    jump: function(obj, first){ //触发分页后的回调
                        if(!first){ //点击跳页触发函数自身，并传递当前页：obj.curr
                            demo(obj.curr);
                        }
                    }
                });
            });
        };

        function  creatHtml(res) {
            var str = '' , last = res.nums;
            for(var i = 0; i < last; i++) {
                //str += '<li>'+ data[i].id +'</li>';
                //console.log(res.content);
                str += '<tr>';
                str += '<td>' + res.content[i].appname + '</td>';
                str += '<td>'+ res.content[i].giftname + '</td>';
                str += '<td  class="key clearfix"><input type="text" value="' + res.content[i].code +'" readonly="readonly"><i class="copyBtn">复制</i></td>';
                str += '<td>' + res.content[i].appname + '</td>';
                str += '</tr>';
                $('#dataList').html(str);
            }
        }
    </script>
    <script src="{{static_res('laypage/laypage.js')}}"></script>
    <script type="text/javascript">

    </script>
@endsection
