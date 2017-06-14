@extends('tob.layout')

@section('content')
    <div class="state clearfix">
        <span class='fl second_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>-------------------------</span>
        <span class='fl third_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>-------------------------</span>
        <span class='fl first_step'><span class='icon'></span>3  等待审核</span>
    </div>
    <div class='account'>
        <div style="text-align: center; margin:40px auto;">
        @if($merchantInfo["status"]==7)
            <h2>等待审核</h2>
            <span>申请成功，官方工作人员将在1~2个工作日内审核您提交的资料，请耐心等待</span>
        @elseif($merchantInfo["status"]==9)
            <h2>通过审核</h2>
            <span>恭喜您，已经通过审核！当前VRonline账号已经升级成体验店专属账号！</span>
        @elseif($merchantInfo["status"]==5)
            <h2>未通过审核</h2>
            <p>您的账号未通过审核，请编辑后重新提交</p>
            <p>未通过原因：{{ $merchantInfo["reason"] ?? "未知原因" }}</p>
        @endif
        </div>
    </div>
    <div class='sub'>
        @if($merchantInfo["status"]==5)
        <div class='sub_btn'>
            <input type="submit" id="edit" style="margin:0 26%;cursor: pointer;" value="修改资料"/>
        </div>
        @endif
    </div>
@endsection

@section('javascript')
<script type="text/javascript">
$("#edit").click(function(){
    location.href="/enter?edit=1";
});
</script>
@endsection
