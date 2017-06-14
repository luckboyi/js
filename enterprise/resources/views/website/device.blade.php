@extends('layouts.website')
@inject('blade', 'App\Helper\BladeHelper')

@section('title')VRonline设备支持@endsection

@section('content')
<div class="device">
	<div>
		<img src="{{static_res('/website/images/devicebg.png')}}" alt="设备支持">
		<a target="_blank" href="http://mall.deepoon.com/">购买大朋VR</a>
	</div>
</div>
@endsection

@section('javascript')
@endsection