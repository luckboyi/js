@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('content')
<div class="ui segment" style="text-align:center">
<img src="/admincp/semantic/images/lock.jpg" class="ui lock circular image centered">

<p >
抱歉当前没有访问权限 请联系管理员 <a href="{{ $link }}">返回</a>
</p>
</div>


@endsection
@section('javascript')
<script type="text/javascript">
// $('.lock').transition('pulse');
</script>
@endsection
