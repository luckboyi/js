<!DOCTYPE html>
<html lang="zh">
<head>
	<meta charset="utf-8">
	<base href="/">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <title></title>
	<script type="text/javascript" src="http://pic.vronline.com/assets/vrplayer/vr.js"></script>
	<link rel="stylesheet" type="text/css" href="//at.alicdn.com/t/font_qjmu58j8vwt81tt9.css">
</head>
<body>

<div id="vr_player" style="width:800px;height:500px;position: absolute"><i class="iconfont icon-iconfontcha vrplayer-close" style="cursor:pointer;color:#fff;font-size:30px;position: relative;top:35px;right:5px;float:right;z-index:999999"></i></div>

<script type="text/javascript">
play()
var old = false
function play(id) {
	if(old) {
		KrPlayer.remove('mplayer');
	}
	KrPlayer.setup('mplayer', 'vr_player', 'http://www.vronline.com/1/1dc019eefb4a58c2a5e6763a7cb315a1/vrplayer.xml');
	old = true;
}
</script>
</body>
</html>
