@inject('blade', 'App\Helper\BladeHelper')
@extends('admincp.nav')
@section('head')


@endsection
@section('content')


<div style="">
<div class="content" style="margin:50px auto;width:800px;">
<h4>{{ $data['title'] }}</h4>
{!! $data['content'] !!}
</div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">

</script>
@endsection