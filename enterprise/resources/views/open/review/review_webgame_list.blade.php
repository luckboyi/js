@extends('open.nav')
@section('content')
    <!--内容-->
    <div class="container container-list">
        <div class="container-head clearfix">
            <p class="numTag"><span class="{{ $left }}" onclick="Open.goTo('/review/{{ $tag }}/0')">未审核（{{ $offlineNum }}）</span><span class="{{ $right }}" onclick="Open.goTo('/review/{{ $tag }}/1')">已审核（{{ $onlineNum }}）</span></p>
        </div>
        <div class="table-con">
            <table class="personal-table" border="0">
                <tr class="title">
                    <th>产品名称</th>
                    <th>主体名称</th>
                    <th>产品类型</th>
                    <th>联系人</th>
                </tr>
                @foreach ($outlist as $list)
                <tr class="btn-review-app-detail" data-tp="{{ $tag }}" data-id="{{ $list['appid'] }}">
                    <td>{{ $list['name'] }}</td>
                    <td>@if ($list['company']){{ $list['company'] }} @else - @endif</td>
                    <td>{{ isset($category[$list['first_class']]['name']) ? $category[$list['first_class']]['name'] : "" }}</td>
                    <td>{{ $list['contacts'] }}</td>
                </tr>
                @endforeach
            </table>
            <div class="page">
            {!! $webgames->render() !!}
            </div>
        </div>
    </div>
</body>

</html>
@endsection