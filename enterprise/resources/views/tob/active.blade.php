@extends('tob.layout')

@section('content')
    <div class='account'>
        <div style="text-align: center; margin:40px auto;">
            <span class=''>{{ $msg }}</span>
        </div>
    </div>
    <div class='sub'>
        <div class='sub_btn' style="text-align: center;">
        @if(!isset($success))
        <input type="submit" id="validateEmail"  style="cursor: pointer;" name='' value="重新验证"/>
        @else
        <input type="submit" id="submitAudit"  style="cursor: pointer;" name='' value="提交审核"/>
        @endif
        </div>
    </div>
@endsection
<!-- BEGIN PAGE CONTENT-->

@section('javascript')
<script type="text/javascript">
$("#validateEmail").click(function(){
    location.href="/validateEmail";
});


$("#submitAudit").click(function(){
    var ajaxUrl = "/submit";
    $.post(ajaxUrl, {}, function(result) {
        if(result.code == 0){
            Open.showMessage("提交审核成功");
            location.href="/wait";
        } else {
            Open.showMessage(result.msg);
        }
    },"json");
});
</script>
@endsection
