@extends('open.nav')

@section('head')
<link rel="stylesheet" href="{{static_res('/open/style/register.css')}}">
<link rel="stylesheet" href="{{static_res('/open/assets/art-dialog/css/ui-dialog.css')}}" />
<script src="{{static_res('/open/assets/art-dialog/js/dialog-min.js')}}"></script>
<script src="{{static_res('/open/js/common.js')}}"></script>
@endsection


@section('content')
<div class="personal">

<section class='sec'>
    	<div class='message'>
    		<p class='f18 title'>{{$info['type'] == 1 ? "公司信息" : "个人信息"}}</p>
            <table>
                <tr>
                    <td>
                        <span>姓名：</span>
                    </td>
                    <td>{{ $info['name'] }}</td>
                </tr>
                <tr>
                    <td>
                        <span>身份证：</span>
                    </td>
                    <td>{{ $info['idcard'] }}</td>
                </tr>
                <tr>
                    <td>
                        <span>电子邮箱：</span>
                    </td>
                    <td>{{ $info['email'] }}</td>
                </tr>
                <tr>
                    <td>
                        <span>联系地址：</span>
                    </td>
                    <td>{{ str_replace("|", "&nbsp;&nbsp;", $info['address']) }}</td>
                </tr>
                @if ($info['type'] == 1)
                <tr>
                    <td>
                        <span>联系人：</span>
                    </td>
                    <td>{{ $info['contacts'] }}</td>
                </tr>
                @endif
                <tr>
                    <td>
                        <span>手持身份证照片：</span>
                    </td>
                    <td>
                        @if (isset($info['credentials']) && $info['credentials'])
                        <img src="{{$info['credentials']}}">
                        @endif
                    </td>
                </tr>
            </table>
    	</div>
    	<div class='sub'>
            <div class='sub_btn'>
                <input type="submit" name='' value="通过" onclick="javascript:review('pass', '')"/>
                <input type="submit" name='' value="驳回" onclick="javascript:review('deny', '')"/>
            </div>
        </div>
</section>
</div>
<script type="text/javascript">
function review(action, msg)
{
    $.ajax({
        url:"http://open.vronline.com/review/user/{{$info['uid']}}",
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
            window.location.href = '{{url("/review/user")}}';
        }
    });
}
</script>
@endsection