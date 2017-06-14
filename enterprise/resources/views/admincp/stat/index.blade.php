@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')
<script type="text/javascript" src="/admincp/plugin/daterange/moment.js"></script>
<script type="text/javascript" src="/admincp/plugin/daterange/daterange.js"></script>
<link rel="stylesheet" type="text/css" href="/admincp/plugin/daterange/daterange.css" />
@endsection
@section('content')
<div class="ui basic small buttons">
<a href="javascript:;" class="selectBtn" data-start="<?=date('Y-m-d', time())?>" data-end="<?=date('Y-m-d', strtotime('+1 day'))?>"><div class="ui basic  button blue">今日</div></a>
<a href="javascript:;" class="selectBtn" data-start="<?=date('Y-m-d', strtotime('-1 day'))?>" data-end="<?=date('Y-m-d', strtotime('+1 day'))?>"><div class="ui basic  button ">昨日</div></a>
<a href="javascript:;" class="selectBtn" data-start="<?=date('Y-m-d', strtotime('-7 day'))?>" data-end="<?=date('Y-m-d', strtotime('+1 day'))?>"><div class="ui basic  button ">最近7日</div></a>
<a href="javascript:;" class="selectBtn" data-start="<?=date('Y-m-d', strtotime('-30 day'))?>" data-end="<?=date('Y-m-d', strtotime('+1 day'))?>"><div class="ui basic  button ">最近30日</div></a>
</div>

<div class="ui icon input" style="width:350px">
  <input type="text" id="daterange" placeholder="选择日期" value="{{ $start }} 至 {{ $end }}">
    <i class="large search link icon"></i>
</div>


<div class="ui basic small buttons right floated">
<div class="ui basic  button ">总注册：@if($lastRet) {{$lastRet['reg_all']}} @endif</div>
<div class="ui basic  button ">总登陆：@if($lastRet) {{$lastRet['login_all']}}  @endif</div>
<div class="ui basic  button ">总充值：@if($lastRet) {{$lastRet['recharge_all']}}  @endif</div>
<div class="ui basic  button ">总消耗：@if($lastRet) {{$lastRet['consume_all']}}  @endif</div>
<div class="ui basic  button ">游戏总量：@if($lastRet) {{$lastRet['game_buy_all']}}  @endif</div>
</div>


<table class="ui sortable celled table fixed" style="clear:both">
  <thead>
    <tr>
      <th width="10%" title="日期">日期</th>
      <th width="10%" title="注册量">注册量</th>
      <th width="10%" title="登录">登录</th>
      <th width="10%" title="充值">充值</th>
      <th width="10%" title="消耗">消耗</th>
      <th width="10%" title="次日留存">次日留存</th>
      <th width="10%" title="三日留存">三日留存</th>
      <th width="10%" title="七日留存">七日留存</th>
      <th width="10%" title="游戏购买量">游戏购买量</th>
    </tr>
  </thead>
  <tbody>
          @if(!isset($retDate) || empty($retDate))
                <tr><td colspan="9" class="center aligned">暂无数据</td></tr>
          @else
              @foreach ($retDate as $info)
              <tr>
                <td>{{ $info['f_ds'] }}</td>
                <td>{{ $info['reg_num'] }}</td>
                <td>{{ $info['login_cnt'] }}</td>
                <td>{{ $info['recharge_money'] }}</td>
                <td>{{ $info['consume_money'] }}</td>
                <td>{{ $info['d1_num'] }}( @if($info['reg_num'] != 0) {{ round($info['d1_num']/$info['reg_num']*100, 2) }} @else 0 @endif%) </td>
                <td>{{ $info['d3_num'] }}( @if($info['reg_num'] != 0) {{ round($info['d3_num']/$info['reg_num']*100, 2) }} @else 0 @endif%)</td>
                <td>{{ $info['d7_num'] }}( @if($info['reg_num'] != 0) {{ round($info['d7_num']/$info['reg_num']*100, 2) }} @else 0 @endif%)</td>
                <td>{{ $info['game_buy_cnt'] }}</td>
              </tr>
              @endforeach
          @endif
  </tbody>
   <tfoot>
    <tr>
    <th colspan="9">
      {!! $paginator->appends(['action'=> $action,'start' => $start,'end' => $end])->render() !!}
    </th>
  </tr></tfoot>
</table>

@endsection

@section('javascript')
<script type="text/javascript">
var start = "{{ $start }}"
var end = "{{ $end }}"
$(function(){
   $('#daterange').daterangepicker({
    // startDate: moment().startOf('day'),
    // endDate: moment(),
    // minDate: '01/01/2012',    //最小时间
    maxDate : moment(), //最大时间
    dateLimit : {
        days : 30
    }, //起止时间的最大间隔
    showDropdowns : true,
    showWeekNumbers : false, //是否显示第几周
    timePicker : true, //是否显示小时和分钟
    timePickerIncrement : 60, //时间的增量，单位为分钟
    timePicker12Hour : false, //是否使用12小时制来显示时间
    ranges : {
        '今日': [moment().startOf('day'), moment()],
        '昨日': [moment().subtract('days', 1).startOf('day'), moment().subtract('days', 1).endOf('day')],
        '最近7日': [moment().subtract('days', 6), moment()],
        '最近30日': [moment().subtract('days', 29), moment()]
    },
    opens : 'right', //日期选择框的弹出位置
    buttonClasses : [ 'btn btn-default' ],
    applyClass : 'btn-small btn-primary blue',
    cancelClass : 'btn-small',
    // format : 'YYYY-MM-DD HH:mm:ss', //控件中from和to 显示的日期格式
    format : 'YYYY-MM-DD',
    separator : ' 至 ',
    locale : {
        applyLabel : '确定',
        cancelLabel : '取消',
        fromLabel : '起始时间',
        toLabel : '结束时间',
        customRangeLabel : '自定义',
        daysOfWeek : [ '日', '一', '二', '三', '四', '五', '六' ],
        monthNames : [ '一月', '二月', '三月', '四月', '五月', '六月',
                '七月', '八月', '九月', '十月', '十一月', '十二月' ],
        firstDay : 1
    }
    },function(dstart, dend, label){
        // start =  dstart.format('YYYY-MM-DD HH:mm:ss');
        // end =  dend.format('YYYY-MM-DD HH:mm:ss');
        start =  dstart.format('YYYY-MM-DD');
        end =  dend.format('YYYY-MM-DD');
    });

  $(".search").click(function() {
      loadData(start,end)
  })
});

$(".selectBtn").click(function() {
      var dstart = $(this).attr("data-start"),
          end = $(this).attr("data-end");
      loadData(dstart,end)
  })

function loadData(dstart,dend) {
  location.href = '/stat/index?action=getselect&start=' +dstart+'&end='+dend;
}
</script>
@endsection
