@extends('layouts.third')

@section('content')
    <div class="state clearfix">
        <span class='fl second_step'><span class='icon'></span>1 填写资料</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl third_step'><span class='icon'></span>2 验证邮箱</span>
        <span class='fl line'>-----------------------------------</span>
        <span class='fl first_step'><span class='icon'></span>3  {{ $devInfo['statVal'] }} </span>
    </div>

        <div class='account'>
            <div style="text-align: center; margin:40px auto;">
                <h2>完成验证！</h2>
                @if($devInfo['stat']==0)
                <span>你的开发者信息已经注册完毕，请提交审核 <a href="/userApply/edit" style="text-decoration: underline">修改资料</a></span>
                @elseif($devInfo['stat']==1)
                <span>你的开发者信息正在审核，请耐心等待</span>
                @elseif($devInfo['stat']==3)
                <span>你的开发者信息审核失败 {{ $devInfo['msg'] }} <a href="/userApply/edit" style="text-decoration: underline">修改资料</a></span>
                @elseif($devInfo['stat']==5)
                 <span>你的开发者信息已经成功 </span>
                @endif
            </div>
        </div>
        @if($devInfo['stat']==0 || $devInfo['stat']==3)
        <div class='sub'>
            <div class='sub_btn'>
                <input type="submit" onclick="gopreview()" name=''style="margin:0 26%;cursor: pointer;" value="提交审核"/>
            </div>
        </div>
        @endif

        <script type="text/javascript">
        function gopreview() {
            $.post("/apply/goReviewUser",function(data) {
                if(data.code==0) {
                    Open.showMessage("提交审核成功",2000,function() {
                         location.reload();
                     });
                } else {
                    Open.showMessage(data.msg);
                }
            },"json")
        }
        </script>
@endsection