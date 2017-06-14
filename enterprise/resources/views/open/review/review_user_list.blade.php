@extends('open.nav')
@section('content')
    <!--内容-->
    <div class="container container-list">
        <div class="container-head clearfix">
            <p class="numTag">
                <!-- <span class="@if(isset($reviewStat) && $reviewStat == 0) online @else offline @endif" onclick="Open.goTo('/review/regUser/0')">未提交审核（@if(isset($offlineNum)) {{ $onlineNum[0] }}  @else 0 @endif）</span> -->
                <span class="@if(isset($reviewStat) && $reviewStat == 1) online @else offline @endif" onclick="Open.goTo('/review/regUser/1')">未审核（@if(isset($offlineNum)) {{ $onlineNum[1] }}  @else 0 @endif）</span>
                <span class="@if(isset($reviewStat) && $reviewStat == 5) online @else offline @endif" onclick="Open.goTo('/review/regUser/5')">已审核（@if(isset($onlineNum)) {{ $onlineNum[5] }} @else 0 @endif）</span>
                <!-- <span class="@if(isset($reviewStat) && $reviewStat == 9) online @else offline @endif" onclick="Open.goTo('/review/regUser/9')">删除审核（@if(isset($onlineNum)) {{ $onlineNum[9] }} @else 0 @endif）</span> -->
            </p>
        </div>
        <div class="table-con">
            <table class="personal-table" border="0">
                <tr class="title">
                    <th>用户id</th>
                    <th>注册类型</th>
                    <th>姓名/公司名称</th>
                    <th>身份证号码/营业执照号码</th>
                    <th>联系人</th>
                    <th>电子邮箱</th>
                </tr>
                @if($outlist)
                @foreach ($outlist as $list)
                <tr class="btn-review-user-detail" data-id="{{$list['uid']}}">
                    <td>{{ $list['uid'] }}</td>
                    <td>@if ($list['type'] == 1) 企业 @else 个人 @endif</td>
                    <td>{{ $list['name'] }}</td>
                    <td>{{ $list['idcard'] }}</td>
                    <td>{{ $list['contacts'] }}</td>
                    <td>{{ $list['email'] }}</td>
                </tr>
                @endforeach
                @else
                <tr><td colspan="6">暂无数据</td></tr>
                @endif
            </table>
            <div class="page">
            {!! $users->render() !!}
            </div>
        </div>
    </div>
</body>

</html>
@endsection
