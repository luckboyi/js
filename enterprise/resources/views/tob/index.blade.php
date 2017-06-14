@extends('tob.client_layout')

@section('meta')
<title>VRonline</title>
@endsection

@section("head")
<script type="text/javascript" src="{{ static_res('/assets/qr/qrcode.min.js') }}"></script>
@endsection

@section('content')
<div class="con">
<!--内容-->
<div class="container clearfix">
	<div class="fl leftCon pr">
		<div class="screen clearfix">
			<i class="fl"></i>
			<span class="fl f14">筛选</span>
		</div>
		<div class="type_selection">
			<span class="f14">热度筛选：</span>
			<ul class="clearfix search-tab" param="hot">
				<li class="fl cur" param-val="" title="按游戏体验次数"><a href="javascript:;">本地</a></li>
				<li class="fl" param-val="rank" title="按游戏体验次数"><a href="javascript:;">综合</a></li>
				{{-- <li class="fl" param-val="rank"><a href="javascript:;">排名</a></li> --}}
			</ul>
		</div>
	<div class="type_selection">
	    <span class="f14">字母筛选：</span>
	    <ul class="clearfix search-spell search-tab" param="spells">
	    	<li class="fl cur" param-val="全部"><a href="javascript:;">全部</a></li>
	        <li class="fl" param-val="ABC"><a href="javascript:;">ABC</a></li>
	        <li class="fl" param-val="DEF"><a href="javascript:;">DEF</a></li>
	        <li class="fl" param-val="GHI"><a href="javascript:;">GHI</a></li>
	        <li class="fl" param-val="JKL"><a href="javascript:;">JKL</a></li>
	        <li class="fl" param-val="MN0"><a href="javascript:;">MN0</a></li>
	        <li class="fl" param-val="PQR"><a href="javascript:;">PQR</a></li>
	        <li class="fl" param-val="STU"><a href="javascript:;">STU</a></li>
	        <li class="fl" param-val="VWX"><a href="javascript:;">VWX</a></li>
	        <li class="fl" param-val="YZ"><a href="javascript:;">YZ</a></li>
	    </ul>
	</div>
	<div class="number clearfix">
		<div>
			<i class="fl"></i>
			<span class="fl">编号：</span>
			<span class="fl">{{ $terminal_no }}号机</span>
		</div>
	</div>
	</div>
	<div class="rightCon">
		<div id="scrollbar1">
		    <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
		    <div class="viewport">
		        <div class="overview search-content">
					<p class="title"><span class="f14 search-title">全部</span></p>
					<ul class="clearfix">
						@foreach($games as $game)
						<li class="fl" data-val="{{ $game['id'] }}" id="qrcode-{{ $game['id'] }}">
							<a href="javascript:;">
								<img src="{{ static_image($game['image']['logo']) }}" >
								<p class="clearfix describe">
									<span class="fl">{{ $game['name'] }}</span>
									<span class="fr"></span>
								</p>
								<div class="details">
									<div class="clearfix frequency">
										<p class="fl">
											<span class="f14">体验次数：</span>
											<span class="f16">1200</span>
											<span class="f14">次</span>
										</p>
										<p class="fr">
											<span>上线时间：</span>
											<span>{{ date("Y-m-d",$game['publish_date']) }}</span>
										</p>
									</div>
									<p class="text" title="">{{ $game['desc'] }}</p>
									<div class="clearfix use">
										<div>
											<div class="fl code qrcode-img"></div>
											<div class="fl price-con">
												<p>体验价格：</p>
												{!! $productPHtml !!}
											</div>
										</div>
									</div>
								</div>
							</a>
						</li>
						@endforeach
					</ul>
		        </div>
		    </div>
		</div>
	</div>

	<div class="box" style="display:none">
		<div class="prompt">
			<p class="clearfix">
				<span class="fl f18">扫码付款</span>
				<span class="fr" onclick="hideBox()"></span>
			</p>
			<div class="promptCon">
				<p class="mode">
					<span class="cur">时间付费</span>
				</p>
				<div class="qrcode"></div>
				<div class="price clearfix">
					<span class="fl">体验价格：</span>
					<ul class="fl clearfix">
					{!! $productLHtml !!}
					</ul>
				</div>
			</div>
		</div>
	</div>
</div>

@endsection

@section('javascript')
<script>
	var merchantid = "{{ $merchantid }}";
	var terminal_sn = "{{ $terminal_sn }}";
	var qrcode;
	var qrcodes = {};
	var productPHtml = '{!! $productPHtml !!}'
	var base_url = 'http://tob.vronline.com/pay/#/index/'+merchantid+'/'+terminal_sn+'/';
    $(function(){
        $('#scrollbar1').tinyscrollbar();
        $(".search-tab li").click(function() {
        	var that = $(this)
        	//var spells = that.find('a').text();
        	var params={};
        	that.addClass('cur').siblings().removeClass('cur');
        	$(".search-tab li.cur").each(function(i,e){
        		params[$(this).parent().attr("param")]=$(this).attr("param-val");
        	});
        	if(params.spells && params.spells=="全部"){
        		params.spells="";
        	}
        	$.post('/search/'+merchantid+'/'+terminal_sn,params,function(res){
        		if(!params.spells){
        			params.spells="全部";
        		}
        		$('.search-title').text(params.spells);
        		var container = $('.search-content ul')
        		container.empty();
        		qrcodes = {};
        		if(typeof(res.data)!='undefined') {
        			$.each(res.data,function(a,b){
        				container.append(addLi(b))
        			})
        		}
        	},"json")
        });
        $(document).on("mouseover",".search-content li",function(){
        	var that = $(this)
        	var qrcode_id = that.attr('data-val');
        	makeCode(qrcode_id,that.find('.qrcode-img'))
        });
         $(document).on("click",".search-content li",function(){
        	var that = $(this)
        	var qrcode_id = that.attr('data-val');
        	showBox(qrcode_id)
        });
    });

    function addLi(data) {
    	var date = new Date(data.publish_date);
    	Y = date.getFullYear() + '-';
		M = (date.getMonth()+1 < 10 ? '0'+(date.getMonth()+1) : date.getMonth()+1) + '-';
		D = date.getDate() + ' ';
		h = date.getHours() + ':';
		m = date.getMinutes() + ':';
		s = date.getSeconds();
    	var dddate =Y+M+D+h+m+s;
    	var html = ''+
    	'<li class="fl" data-val="'+data.id+'"><a href="javascript:;"><img src="'+static_image(data.image.logo)+'" >'+
		'<p class="clearfix describe"><span class="fl">'+data.name+'</span><span class="fr"></span></p>'+
		'<div class="details"><div class="clearfix frequency"><p class="fl"><span class="f14">体验次数：</span>'+
		'<span class="f16">1200</span>'+
		'<span class="f14">次</span>'+
		'</p><p class="fr"><span>上线时间：</span><span>'+dddate+'</span></p></div>'+
		'<p class="text" title="">'+data.desc+'</p>'+
		'<div class="clearfix use"><div><div class="fl code qrcode-img"  ></div>'+
		'<div class="fl price-con"><p>体验价格：</p>'+productPHtml+
		'</div></div></div></div></a></li>'
    	return html;
    }

    function showBox(qrcode_id) {
    	$.get("/terminal/check",{merchantid:merchantid,terminal_sn:terminal_sn},function(res) {
    		if(res.code==0) {
    			console.log(qrcode_id);
    			window.CppCall('gameframe', 'startgame','{"gameid":'+qrcode_id+'}')
    		} else {
    			var obj = $(".box .qrcode");
		    	obj.empty();
		    	qrcode = new QRCode(obj[0], {
					width : 150,
					height : 150
				});
				console.log(base_url+qrcode_id);
		   		qrcode.makeCode(base_url+qrcode_id);
		    	$(".box").show();
    		}
    	},"json");
    }

    function hideBox() {
    	$(".box").hide();
    }

    function makeCode(qrcode_id,obj) {
    	if(typeof(qrcodes[qrcode_id])!="undefined") {
    		return;
    	}
    	qrcodes[qrcode_id] = 1;
		qrcode = new QRCode(obj[0], {
			width : 70,
			height : 70
		});
   		qrcode.makeCode(base_url+qrcode_id);
   }
</script>
@endsection
