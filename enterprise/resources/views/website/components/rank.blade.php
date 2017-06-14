@inject('blade', 'App\Helper\BladeHelper')
<div class="hotList_con {{$type}}_hot @if(!isset($hide)) cur @endif">
	<ul>
        @if(isset($data) && is_array($data))
		@foreach($data as $k=>$v)
		<li {!! $blade->handleRecommendAttr($v,["class"=>$k==0?"cur":""]); !!}>
			<a class="clearfix" href="javascript:;">
				<span class="fl ranking ranking{{$k+1}}"></span>
				<span class="fl" title="网页游戏名称">{{$v["name"]}}</span>
				<span class="fr downNum">{{$v["play"]}}次</span>
				<div>
					@if($type=="video")
					<img src="{{static_image($v["image"]["cover"],'1-228-60')}}" >
					@else
					<img src="{{static_image($v["image"]["rank"])}}" >
					@endif
					@if($type=="vrgame")
					<p>
						<span><em>{{number_format($v["score"], 1)}}</em>分</span>
						<span>{{isset($v["sell"]) && number_format($v["sell"], 2)*$payRate > 0?number_format($v["sell"], 2)*$payRate . 'V':"免费"}}</span>
					</p>
					@endif
				</div>
			</a>
		</li>
		@endforeach
        @endif
	</ul>
</div>
