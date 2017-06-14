@extends('layouts.admin')

@section('meta')
<meta name="csrf-token" content="{{ csrf_token() }}">
@endsection

@section('css')
<link href="{{asset('assets/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<h3 class="page-title">
总数据
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
        总数据
    </li>
</ul>

<div class="widget yellow">
    <div class="widget-title">
        <h4><i class="icon-reorder"></i> 总数据</h4>
    </div>
    <div class="widget-body">
        <div class="row-fluid">
            <div class="span6">
                <div class="control-group dataDateChoice">
                    <label class="control-label" style="float: left;line-height: 30px;">选择时间：</label>
                    <div class="controls">
                        <div class="input-append date" date-data="" data-date-format="yyyy-mm-dd">
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
                            <td>总注册：@if($lastRet) {{$lastRet['reg_all']}} @endif</td>
                            <td>总登陆：@if($lastRet) {{$lastRet['login_all']}}  @endif</td>
                            <td>总充值：@if($lastRet) {{$lastRet['recharge_all']}}  @endif</td>
                            <td>总消耗：@if($lastRet) {{$lastRet['consume_all']}}  @endif</td>
                            <td>游戏总量：@if($lastRet) {{$lastRet['game_buy_all']}}  @endif</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <table class="table table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th>日期</th>
                    <th>注册量</th>
                    <th>登录</th>
                    <th>充值</th>
                    <th>消耗</th>
                    <th>次日留存</th>
                    <th>三日留存</th>
                    <th>七日留存</th>
                    <th>游戏购买量</th>
                </tr>
            </thead>
            @if(isset($retDate))
                @foreach($retDate as $info)
                    <tr>
                        <td>{{ $info['f_ds'] }}</td>
                        <td>{{ $info['reg_num'] }}</td>
                        <td>{{ $info['login_cnt'] }}</td>
                        <td>{{ $info['recharge_money'] }}</td>
                        <td>{{ $info['consume_money'] }}</td>
                        <td>{{ $info['d1_num'] }}({{ round($info['d1_num']/$info['reg_num']*100, 2) }}%)</td>
                        <td>{{ $info['d3_num'] }}({{ round($info['d3_num']/$info['reg_num']*100, 2) }}%)</td>
                        <td>{{ $info['d7_num'] }}({{ round($info['d7_num']/$info['reg_num']*100, 2) }}%)</td>
                        <td>{{ $info['game_buy_cnt'] }}</td>
                    </tr>
                @endforeach
            @endif
        </table>
         {!! $paginator->appends(['action'=> $action,'date' => $date])->render() !!}
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
    window.location.href = 'http://admin.vronline.com/data/all?action=getsomeday&date=' + $('.seachTm').val();
});
</script>
@endsection
