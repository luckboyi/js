@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')

    <style>
    #shortInput{
        width: 40px;
    }
      .whole p{
        text-indent: 2em;
        font-size: 14px;
        color:#4d4d4d
      }
      .table{
        margin-left: 30px;
        font-size: 14px;
        color: #4d4d4d;
      }
      .strong{
        font-size: 16px;
        font-weight: bold;
      }
      .whole{
        width: 100%;
        height: auto;
      }
      .header{
        font-size: 24px;
        margin: 0 auto;
        text-align: center;
      }
      .tdTitle{
        width: 150px;
        text-align: center;
      }
      .supply{
        text-align: center;
        font-size: 18px;
      }
      input.shortInput{
        width: 40px;
      }
    </style>
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
                <i><a href='{{url("/review/$tag/info/right/$appid")}}'>软件著作权</a></i>
                <i class="cur">电子合同</i>
            </span>
        </h4>
        <div class="content product-detail product-detail-info">
            <div class="product-row clearfix">
                {!! $blade->showProtocol("", $webgame["agreement"]) !!}
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