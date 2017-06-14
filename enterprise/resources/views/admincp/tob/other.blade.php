@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')

@section('head')
<script type="text/javascript" src="{{ static_res('/assets/loi/loiform.js') }}"></script>
@endsection

@section('content')

<div class="ui grid" style="clear:both">
  <div class="six wide column">
  <h5 class="ui top attached header">
  价格设定
  </h5>
  <div class="ui attached  segment">
    <form class="ui form"  onsubmit="return false;">
      <input id="id" type="hidden" >
        <div class="three fields">
          <div class="field">
          <label>默认价格(RMB)</label>
          <input id="price" type="text" value="{{ $product[0] }}">
          </div>
          <div class="field">
          <label>默认时间(秒)</label>
          <input id="time" type="text" value="{{ $product[1] }}">
          </div>
           <div class="field">
          <label>描述</label>
          <input id="desc" type="text" value="{{ $product[2] }}">
          </div>
        </div>
        <div class="field">
        <label>最低时间价值(RMB/分)</label>
        <input id="lowrate" type="text" value="{{ $product[3] }}">
        </div>
        <button class="ui button product-save">保存</button>
    </form>
</div>
 </div>
</div>
@endsection
@section('javascript')
<script type="text/javascript">
$(function(){
  $('.product-save').click(function(){
      var price = $("#price").val();
      var time = $("#time").val();
      var desc = $("#desc").val();
      var lowrate = $("#lowrate").val();
      $.post("/json/save/tob_defaultproduct",{price:price,time:time,desc:desc,lowrate:lowrate},function(){
        loiMsg("保存价格设定成功",function(){location.reload();},"success");
      });
  });
})

</script>
@endsection