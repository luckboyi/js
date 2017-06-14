@extends('website.media.layout')
@inject('blade', 'App\Helper\BladeHelper')
@section('title')视频播放@endsection

@section('webgameRight')
	<div class="multimedia_right_con fr video_play_con">

		<div class="video_play_container">
			<div class="valiantPhoto" id="valiantPhoto" data-vid="{{ $videoInfoDet['video_id'] }}" data-video-src="@if(!empty($videoInfoDet)) {{ $videoInfoDet['video_link'] }} @endif" style="width: 976px; height: 540px;background: #000"></div>
		</div>

		<div class="in_video_information">
			<div class="video_content">
				<div class="video_content_head clearfix">
					<h4 class="fl">{{ $videoInfoDet['video_name'] }}</h4>
					<a style="display: none"> @if(isset($clickRet)) {{ json_encode($clickRet) }} @endif </a>
					<div id="disagree"  class="fr pr unlike @if(isset($clickRet) && $clickRet['alreadyClick'] == 'N') cur @endif" style="padding: 0 2px"><b id="disagreenum"> {{ $videoInfoDet['disagreenum'] }}</b></div>
					<div id="agree" class="fr pr like @if(isset($clickRet) && $clickRet['alreadyClick'] == 'Y') cur @endif" style="padding: 0 2px"><b id="agreenum">{{ $videoInfoDet['agreenum'] }}</b></div>
					<p class="fr clearfix"><b class="fl" style="padding: 0 2px;">{{ $videoInfoDet['video_view'] }}</b><i class="fl language" data-name="haveWatch">次观看</i></p>
				</div>
			</div>
			<div class="video_intro">{{ $videoInfoDet['video_intro'] }}</div>
		</div>
	</div>
@endsection


@section('javascript-media')
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
		var vid = $('#valiantPhoto').attr("data-vid");
		var url = 'http://www.vronline.com/video/addSupport?json={"vid":'+ vid +',"support":1}';

		$.ajax({
			type: "GET",
			url: url,
			data: {},
			dataType: "json",
			success: function(data){
				if(data.code == 0) {
					var agreeNum = $("#agreenum").text();
					$("#agreenum").text(parseInt(agreeNum) + 1);
					$("#agree").addClass('cur');
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
		var vid = $('#valiantPhoto').attr("data-vid");
		var url = 'http://www.vronline.com/video/addSupport?json={"vid":'+ vid +',"support":0}';

		$.ajax({
			type: "GET",
			url: url,
			data: {},
			dataType: "json",
			success: function(data){
				if(data.code == 0) {
					var disagreeNum = $("#disagreenum").text();
					$("#disagreenum").text(parseInt(disagreeNum) + 1);
					$("#disagree").addClass('cur');
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
	var ev = e || window.event;
	var appid = "@if(isset($_GET['vid'])) {{ $_GET['vid'] }} @endif";
	var getTimeLen = $('timeLabel').text();

	var timelen = getTimeLen;
	console.log(timelen);
	if(timelen == '') {
		timelen = "00:00:20/00:05:20";
	}

	//ev && (ev.returnValue = timelen);

	var timelenArr = timelen.split('/');

	var h,m,s;
	secondArr = timelenArr[0].split(':');
	if(secondArr.length > 2) {
		$.each(secondArr, function(key, val) {
			if(key == 0){
				h = val*3600;
			}
			if(key == 1){
				m = val*60;
			}
			if(key == 2){
				s = val;
			}
		});
	} else {
		$.each(secondArr, function(key, val) {
			h = 0;
			if(key == 0){
				m = val*60;
			}
			if(key == 1){
				s = val;
			}
		});
	}

	var secondTotal = parseInt(h) + parseInt(m) + parseInt(s);
	//ev && (ev.returnValue = secondTotal);
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
@yield("javascript-webgame")
@endsection
