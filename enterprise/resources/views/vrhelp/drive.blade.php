@extends('vrhelp.layout')
@section('meta')
<title>游戏驱动</title>

@endsection

@section('head')

@endsection

@section('content')
<div class="main_container clearfix">
      <div class="grid drive oh">
      <div class="group clearfix">
      <div class="fl drive_eq">
      <ul>
      <li class="tac"><span class="drive_name" style="font-size:34px">欢迎使用 {{ $drive['name'] }}</span></li>
      <li class="eq_pic" style="margin-top:3px"><table><tr><td align="center" valign="middle"><img src="{{ static_res($drive['icon']) }}"></td></tr></table></li>
      <li class="result status_complete" style="display:none;margin-top:10px"><span class="drive_connect">已连接</span></li>
      <li class="result status_install" style="display:none;margin-top:10px"><div class="drive_bar mb20 ing"><p class="drive_pro pr"><i class="in_pro pa percent_width" style="width: 0%"></i><b class="pa f16 percent">0%</b></p><p class="f16">设备已接入，正在自动安装驱动，请耐心等待...</p></div><div class="drive_bar ed" style="display: none"><p class="drive_pro pr"><i class="in_pro pa" style="width: 100%"></i><b class="pa f16">100%</b></p><p class="f16">安装成功！</p></div</li>
      <li class="result status_fail" style="display:none;margin-top:10px"><p class="drive_fail clearfix"><i class="drive_icon fl"></i><span class="fl f24">驱动安装失败</span><button type="button" class="fr f14" onclick="reinstall()">重试</button></p>
      <p class="f14 drive_way"><span class="red f18">安装驱动失败可能怎么办？</span><br>1、将设备插拨后，重新连接电脑<br>2、检查设备是否有损坏或异常。<br><br>仍未解决，请联系VR助手官方运营群，我们会有专职人员为您
解决当前问题。</p></li>
      <!--安装失败 end-->
      </ul>
      </div>
      <div class="fr">
      <div class="drive_con pr">
      <ul>
      <li class="drive_con_t f20 tac">推荐电脑配置</li>
      <li class="f20 tac pr"><i class="triangle pa"></i>软件</li>
      <li class="drive_con_t f16">
      <table width="100%" border="0">
      <tr>
      <td width="120" valign="top">操作系统版本</td>
      <td>Windows 7 SP1 64位 家庭版/专业版/旗舰版</td>
      </tr>
      <tr>
      <td width="120" valign="top">&nbsp;</td>
      <td>Windows 8.1 64位 标准版/专业版</td>
      </tr>
      <tr>
      <td width="120" valign="top">&nbsp;</td>
      <td>Windows 10 64位 家庭版/专业版</td>
      </tr>
      </table>
      </li>
      <li class="f20 tac pr"><i class="triangle pa"></i>硬件</li>
      <li class="drive_con_t f16">
      <table width="100%" border="0">
      <tr>
      <td colspan="2" class="tac">游戏用户</td>
      </tr>
      <tr>
      <td width="120" valign="top">推荐配置</td>
      <td>独立显卡台式机或笔记本</td>
      </tr>
      <tr>
      <td width="120" valign="top">处理器</td>
      <td>i5-4590或AMD FX 8350同等或更高</td>
      </tr>
      <tr>
      <td width="120" valign="top">显卡</td>
      <td>Nvidia GeForce GTX970或AMD Radeon R9 290同等或更高</td>
      </tr>
      <tr>
      <td width="120" valign="top">内存</td>
      <td>4G同等或更高</td>
      </tr>
      <tr>
      <td width="120" valign="top">场景说明</td>
      <td>大多数VR游戏能够流畅运行,3D影视和360度全景视频可流畅体验</td>
      </tr>
      </table>
      </li>
      <li class="pr"><i class="triangle pa"></i></li>
</ul><div class="drive_bg pa"></div></div></div>
      </div>
      <div class="group drive_rec"><span class="f18"><i class="fl"></i>为你推荐</span>
      <ul>
        @foreach($recommend as $item)
            <li><a href="JavaScript:;" style="background-image:url('{!! static_image($item['image']['logo']) !!}');" class="pr game_detail" data-val="{{ $item['id'] }}"><span class="f16 pa">{!!$item['name']!!}</span></a></li>
        @endforeach
      </ul></div>
      </div>
    </div>
@endsection


@section('javascript')

<script type="text/javascript">
var last = 0;
 window.addEventListener('message', function(event){
   if(typeof(event.data)=="object") {
    var res = event.data
    if(typeof(res.tp)!="undefined") {
        switch (res.tp) {
            case "drive_progress":
                  if(res.data.pro!=last) {
                        last = res.data.pro
                        $('.status_install').show().siblings('.result').hide();
                        $('.status_install .percent').text(res.data.pro+"%")
                        $('.status_install .percent_width').css('width',res.data.pro+"%");
                        if(res.data.pro==100) {
                              $('.drive_bar.ing').hide();
                              $('.drive_bar.ed').show();
                        }
                  }
            break;
            case "drive_install_fail":
                  $('.status_fail').show().siblings('.result').hide();
            break;
        }
    }
   }
}, false);

$(function(){
      loadStatus()
})

function loadStatus() {
      var status = parent.drive.getStatus()
      $('.status_'+status).show().siblings('.result').hide();
}

function reinstall() {
      parent.drive.reinstall();
}


</script>
@endsection
