@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<h3 class="page-title">
页游数据
</h3>
<ul class="breadcrumb">
    <li>
        <a href="#">首页</a>
        <span class="divider">/</span>
    </li>
    <li>
        <a href="#">数据查询</a>
        <span class="divider">/</span>
    </li>
    <li class="active">
        页游数据
    </li>
</ul>

<div class="widget purple">
    <div class="widget-title">
        <h4><i class="icon-reorder"></i> 页游数据</h4>
    </div>
    <div class="widget-body">
        <div class="row-fluid">
            <div class="span6">
                <div class="control-group dataDateChoice">
                    <label class="control-label" style="float: left;line-height: 30px;">选择时间：</label>
                    <div class="controls">
                        <div class="input-append date" date-data="" data-date-format="yyyymmdd">
                            <input class="medium seachTm" name="ed"  type="text" value="@if(isset($_GET['date'])) {{$_GET['date']}} @endif" readonly="readonly">
                            <span class="add-on"><i class="icon-th"></i></span>
                        </div>
                        <button class="btn seachBtn" type="submit" style="margin-bottom: 10px;height: 30px;"><i class="icon-search"></i> </button>
                    </div>
                </div>
            </div>
            <div class="span6">
                <table class="table table-striped table-hover table-bordered">
                    <tbody>
                        <tr>
                            <td>总注册：</td>
                            <td>总登陆：</td>
                            <td>总充值：</td>
                            <td>总消耗：</td>
                            <td>视频总量：</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <table class="table table-striped table-hover table-bordered">

            @if(isset($adminVideoInfo) && !empty($adminVideoInfo))
                <thead>
                    <tr>
                        <th>日期</th>
                        <th>点播总量</th>
                        <th>点播人数</th>
                        <th>点播总时长(s)</th>
                        <th>点播消耗金额</th>
                    </tr>
                </thead>
                @foreach($adminVideoInfo as $info)
                    <tr>
                        <td>{{ $info['date'] }}</td>
                        <td>{{ $info['playnum'] }}</td>
                        <td>{{ $info['usercount'] }}</td>
                        <td>{{ $info['totaltm'] }}</td>
                        <td>0</td>
                    </tr>
                @endforeach
            @else
                <h3>无该日期数据！</h3>
            @endif

        </table>
        {!! $adminVideoInfo->render() !!}
    </div>
</div>
@endsection

@section('javascript')
<script src="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js')}}"></script>
<script src="{{asset('assets/bootstrap-datetimepicker/locales/bootstrap-datetimepicker.zh-CN.js')}}" ></script>
<script type="text/javascript">
$(".date").datetimepicker({
    minView: "month",//设置只显示到月份
    format : "yyyy-mm-dd",//日期格式
    autoclose:true,//选中关闭
    todayBtn: true//今日按钮
});

$('.seachBtn').on('click', function(){
    console.log($('.seachTm').val());
    window.location.href = 'http://admin.vronline.com/data/video?date=' + $('.seachTm').val();
});
</script>
@endsection
