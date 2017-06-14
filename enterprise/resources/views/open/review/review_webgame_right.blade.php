@extends('open.nav')
@section('content')
<!--内容-->
<div class="container">
    <div class="in_con">
        <h4 class="f14">产品详情</h4>
        <div class="content product-detail pr">
            <p>
                <span class="product-title">{{$webgame['name']}}</span>
                <span class="product-id">产品ID：{{$webgame['appid']}}</span>
                <span class="product-status success"><i></i>{{$webgame['status_dec']}}</span>
            </p>
            <p>
                <span>创建时间：{{ $webgame['ctime'] }}</span>
                @if ($webgame['push_time'] > 0)
                <span>上线时间：{{ date("Y-m-d H:i:s", $webgame['push_time']) }}</span>
<!--                     <span class="product-status fail"><i></i>已上线</span> -->
                @else
                <span class="product-status fail"><i></i>未上线</span>
                @endif
            </p>
            @if(isset($webgame['stat']) && $webgame['stat'] == 1)
            <p class="pa sub_btn">
                <span class="pass" onclick="javascript: review('pass', '');">通过</span>
                <span class="reject" onclick="javascript: review('deny', '');">驳回</span>
            </p>
            @endif
        </div>
    </div>
    <div class="in_con">
        <h4 class="f14 clearfix pr">
            <span class="">产品详情</span>
            <span class=" pa nav">
                <i><a href='{{url("/review/$tag/info/base/$appid")}}'>产品详情</a></i>
                <i class="cur">软件著作权</i>
                <i><a href='{{url("/review/$tag/info/agreement/$appid")}}'>电子合同</a></i>
            </span>
        </h4>
        <div class="content product-detail product-detail-info">
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">软件著作权：</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl pr">
                        <span class="pic-icon"></span>
                        @if($webgame["cp_soft"])
                            <a href="{{ $webgame["base"].$webgame["cp_soft"] }}" target="_blank"><img src="{{ $webgame["base"].$webgame["cp_soft"] }}"  class="pa" height="auto" width="100%" /></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏备案通知单：</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if($webgame["cp_record"])
                            <a href="{{ $webgame["base"].$webgame["cp_record"] }}" target="_blank"><img src="{{ $webgame["base"].$webgame["cp_record"] }}"  class="pa" height="auto" width="100%" /></a>
                        @endif
                    </div>
                </div>
            </div>
            <div class="product-row clearfix">
                <p class="clearfix">
                    <span class="title fl">游戏版号通知单：</span>
                </p>
                <div class="pic-con clearfix ">
                    <div class="pic fl">
                        <span class="pic-icon"></span>
                        @if($webgame["cp_publish"])
                            <a href="{{ $webgame["base"].$webgame["cp_publish"] }}" target="_blank"><img src="{{ $webgame["base"].$webgame["cp_publish"] }}"  class="pa" height="auto" width="100%" /></a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
function review(action, msg)
{
    $.ajax({
        url:'{{url("/review/webgame/$appid")}}',
        type:'POST',
        dataType:'json',
        data:{ action: action, msg: msg },
        success:function(data){
            if (data.code == 0)
            {
                alert("审核成功");
            }else {
                alert(data.msg);
            }
            console.log(data);
            window.location.href = '{{url("/review/$tag")}}';
        }
    });
}
</script>
@endsection