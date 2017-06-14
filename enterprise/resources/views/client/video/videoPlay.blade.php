@extends('client.video.layout')
@section('title')多媒体@endsection
@section('content')
	<div class="multimedia_right_con  video_con_hei pr"  id="video_right_con">
		<div class="scrollbar  video_con_hei pa fr"><div class="track"><div class="thumb pa"><div class="end"></div></div></div></div>
		<div class="viewport  video_con_hei">
			<div class="overview">
				<div class="video_play_container">
					<div class="valiantPhoto" id="valiantPhoto" data-video-src="@if(!empty($videoInfoDet)) {{ $videoInfoDet['video_link'] }} @endif" style="width: 976px; height: 540px;background: #000"></div>
				</div>
				<div class="in_video_information">
					<div class="video_content">
						<div class="video_content_head clearfix">
							<h4 class="fl">{{ $videoInfoDet['video_name'] }}</h4>
							<div id="disagree"  class="fr pr unlike @if(isset($clickRet) && $clickRet['alreadyClick'] == 'Y') cur @endif" style="padding: 0 2px"><b id="disagreenum"> {{ $videoInfoDet['agreenum'] }}</b></div>
							<div id="agree" class="fr pr like @if(isset($clickRet) && $clickRet['alreadyClick'] == 'N') cur @endif" style="padding: 0 2px"><b id="agreenum">{{ $videoInfoDet['disagreenum'] }}</b></div>
							<p class="fr clearfix"><b class="fl" style="padding: 0 2px;">{{ $videoInfoDet['video_view'] }}</b><i class="fl language" data-name="haveWatch">次观看</i></p>
						</div>
					</div>
					<div class="video_intro">{{ $videoInfoDet['video_intro'] }}</div>
				</div>
			</div>
		</div>
	</div>

@endsection

@section('javascript')
<script type="text/javascript">

	$(function(){
			//vr视频播放
			$('#valiantPhoto').Valiant360({
				clickAndDrag:true,
				muted:false,
				loop:false
			});
		})

	$(".choice-tab").click(function(){
		$(this).addClass('tab_titleBg').siblings().removeClass('tab_titleBg');
		var id=$(this).attr("tag-id");
		$(".tag-content").hide();
		$("#"+id).show();
	})
	//判断是否有cur这个属性
	function ifExistCur(){
		var id = $("#agree,#disagree");
		return id.hasClass('cur');
	}


	$("#agree").click(function(){
		var ifLogin = "{{ $uid }}";
	//	if(ifLogin == '') {
	//		alert("请先登录");
	//		return false;
	//	}
		if(!ifExistCur()) {
			var url = 'http://www.vronline.com/video/addSupport?json={%22vid%22:10022,%22support%22:1}';

			$.ajax({
				type: "GET",
				url: url,
				data: {},
				dataType: "json",
				success: function(data){
					if(data.code == 0) {
						var agreeNum = $("#agreenum").text();
						$("#agreenum").text(parseInt(agreeNum) + 1);
						this.addClass('cur');
					} else {
						alert(data.msg);
					}
				}
			});
		} else {
			alert("您已经点过了！");
		}
	})
	$("#disagree").click(function(){
		if(!ifExistCur()) {
			var url = 'http://www.vronline.com/video/addSupport?json={%22vid%22:10022,%22support%22:0}';

			$.ajax({
				type: "GET",
				url: url,
				data: {},
				dataType: "json",
				success: function(data){
					if(data.code == 0) {
						var disagreeNum = $("#disagreenum").text();
						$("#disagreenum").text(parseInt(disagreeNum) + 1);
						this.addClass('cur');
					} else {
						alert(data.msg);
					}
				}
			});
		} else {
			alert("您已经点过了！");
		}
	})
	//记录用户的播放记录
	function addOnBeforeUnload(e) {
		var ev = e || event;
		var appid = "@if(isset($_GET['vid'])) {{ $_GET['vid'] }} @endif";
		var getTimeLen = $('timeLabel').text();

		var timelen = getTimeLen;
		if(timelen == '') {
			timelen = "00:00:10/00:05:20";
		}

//		ev && (ev.returnValue = timelen);

		var timelenArr = timelen.split('/');

		var h,m;
		secondArr = timelenArr[0].split(':');

		$.each(secondArr, function(key, val) {

			if(key == 0){
				h = val*3600;
			}
			if(key == 1){
				m = val*60;
			}
		});
		var secondTotal = parseInt(h) + parseInt(m) + parseInt(secondArr[2]);
		//ev && (ev.returnValue = secondArr[0]);
		if(secondTotal > 0) {
			$.post('{{ url("video/addHistory") }}',{'_token':'{{csrf_token()}}','appid':appid,'timelen':secondTotal},function(data) //第二个参数要传token的值 再传参数要用逗号隔开
			{
				console.log(data);
			});
		}
	}


	if(window.attachEvent){
		window.attachEvent('onbeforeunload', addOnBeforeUnload);
	} else {
		window.addEventListener('beforeunload', addOnBeforeUnload, false);
	}
	</script>

@endsection

