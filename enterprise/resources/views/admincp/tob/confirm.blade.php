@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')
<div class="ui basic small buttons">
{!! $blade->showHtmlClass('tob_extract_stat',$choose,'menu') !!}
</div>

{{-- <div class="ui icon input" style="width:400px">
  <input type="text" class="action-search" placeholder="搜索商户ID" value="{{ $searchText }}">
  @if($searchText)
  <i class="large remove link icon"></i>
  @else
  <i class="large search link icon"></i>
  @endif
</div> --}}
@if($choose==6  && $num>0)
<div class="ui small button right floated blue confirm-pay" style="margin: 5px auto 20px;"><i class="payment icon"></i> 确认付款（ {{$num}} ）</div>
@endif

<table class="ui sortable celled table fixed">
  <thead>
    <tr>
      <th width="20%">订单ID</th>
      <th width="10%">商户ID</th>
      <th width="10%">用户名称</th>
      <th width="10%">提现金额</th>
      <th width="15%">开户行</th>
      <th width="15%">申请时间</th>
      <th width="10%">状态</th>
      <th width="10%" class="center aligned">操作</th>
    </tr>
  </thead>
  <tbody>
    @if(count($rows)<1)
      <tr><td colspan="8" class="center aligned">暂无数据</td></tr>
    @elseif($error)
      <tr><td colspan="8" class="center aligned">{{$error}}</td></tr>
    @else
      @foreach ($rows as $val)
      <tr>
        <td>{{ $val["orderid"] }}</td>
        <td>{{ $val["merchantid"] }}</td>
        <td>{{ $val["card_name"] }}</td>
        <td>{{ $val["cash"] }}</td>
        <td>{{ $val["card_opener"] }}</td>
        <td>{{ $val["ctime"] }}</td>
        <td>{{ $blade->showHtmlClass('tob_extract_stat',$val["stat"]) }}</td>
        <td class="center aligned" order-id="{{$val["orderid"]}}" merchantid="{{$val["merchantid"]}}" name="{{$val["card_name"]}}" cash="{{$val["cash"]}}">
           @if($choose==0)
          <i class="large check circle icon teal action-audit"></i>
            @endif
        </td>
      </tr>
      @endforeach
    @endif
  </tbody>
   <tfoot>
    <tfoot>
    <tr><th colspan="8">
    <div class="dataTables_paginate paging_bootstrap pagination">
        {!! $pageview !!}
      </div>
    </th>
  </tr>
  </tfoot>
</table>

<div class="ui modal modal-audit">
  <i class="close icon"></i>
  <div class="header">提现审核</div>
  <div class="content">
  <div class="field">
      <div>订单ID：<span class="orderid"></span></div>
      <div>商户ID：<span class="merchantid"></span></div>
      <div>用户名称：<span class="cardname"></span></div>
      <div>提现金额：<span class="cash"></span></div>
      <br />
      <div>是否通过该提现申请</div>
  </div>
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="audit(0)">驳回 </div>
  <div class="ui positive button" onclick="audit(1)">通过 </div>
  </div>
</div>

<div class="ui modal modal-pay">
  <i class="close icon"></i>
  <div class="header">确认付款</div>
  <div class="content">
    是否确认支付已经审核的提现申请（共 {{$num}} 笔）？
  </div>
  <div class="actions">
  <div class="ui negative button" onclick="pay(0)">取消 </div>
  <div class="ui positive button" onclick="pay(1)">确认 </div>
  </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
var audit_id,audit_modal,pay_modal;

$(function(){

  $(".action-audit").click(function() {
    var that = $(this);
    var obj = that.parent();
    audit_id = obj.attr("order-id");
    var modal=$('.ui.modal.modal-audit');
    modal.find(".orderid").text(audit_id);
    modal.find(".merchantid").text(obj.attr("merchantid"));
    modal.find(".cardname").text(obj.attr("name"));
    modal.find(".cash").text(obj.attr("cash"));
    audit_modal = $('.ui.modal.modal-audit').modal('show');
  });

  $(".confirm-pay").click(function() {
    pay_modal=$('.ui.modal.modal-pay').modal('show');
  })

  $(".action-search").keypress(function() {
    if(event.keyCode==13) {
       var searchText = $(this).val();
       location.href = "/tob/confirm?search="+searchText;
    }
  });

  $(".search.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/tob/confirm?search="+searchText;
  });

  $(".remove.link").click(function() {
    var searchText = $(this).prev().val();
    location.href = "/tob/confirm";
  });

});

function audit(tp) {
  audit_modal.modal('hide');
  console.log(audit_id);
  if(audit_id>0) {
    permPost("/tob/confirm",{order_id:audit_id,tp:tp}, function(data){
      if(tp==1) {
        loiMsg("审核成功",function(){location.reload();},"success");
      } else {
        loiMsg("驳回成功",function(){location.reload();},"success");
      }
    });
  }
}

function pay(tp) {
  pay_modal.modal('hide');
  if(tp==0){
    return false;
  }
  $.post("/tob/payextract",{},function(data){
    if(data.code!=0) {
      loiMsg(data.msg);
    } else {
      loiMsg("付款成功",function(){location.reload();},"success");
    }
  },"json");
}

</script>
@endsection
