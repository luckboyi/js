@inject('blade', 'App\Helper\BladeHelper')
<div class="home_banner webgame_banner">
	<ul class="clearfix">
		@if(isset($data) && is_array($data) && !empty($data))
		@foreach($data as $key=>$banner)
		<li class="fl {{$key==0?"cur":""}}"><a {!! $blade->handleBannerAttr($banner) !!} ><img src="{{ static_image($banner["image"]["cover"],100) }}" ></a></li>
		@endforeach
		@endif
	</ul>
	<div class="thumbnail">
		<ol>
			@if(isset($data) && !empty($data) && is_array($data))
			@foreach($data as $banner)
			<li>
				<a class="clearfix" href="javascript:;">
					<span class="fl"><img src="{{ static_image($banner["image"]["icon"]) }}" ></span>
					<div class="fr">
						<h3>{{ $banner['name'] }}</h3>
						<p title="{{ $banner['desc'] }}">{{ $banner['desc'] }}</p>
					</div>
				</a>
			</li>
			@endforeach
			@endif
		</ol>
	</div>
</div>
