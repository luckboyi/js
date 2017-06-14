@extends('vrhelp.layout')
@section('meta')
<title></title>
@endsection

@section('head')
<link rel="stylesheet" href="{{ static_res('/website/style/valiant360.css') }}">
<script src="{{ static_res('/website/js/three.min.js') }}"></script>
<script src="{{ static_res('/website/js/jquery.valiant360.js') }}"></script>
<style type="text/css">
.ucenter .in_detail_con{
    width:860px;
    height:690px;
    padding: 0;
}
.game_list label{
    cursor: pointer;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 70px;
    display: inline-block;
    text-indent: 0;
}
</style>
@endsection

@section('content')
    <div class="left_item">
        <div class="user_msg_head tac">
            <div class="user_img_con">
                <div class="img_con" >
                    <div class="in_img_con"  style="background-image:url('{{ $user['face'] }}');"></div>
                </div>
            </div>
            <div class="user_name_con tac pr">
                <p class="cp">{{ $user['nick'] }}<i class="triangle" ></i></p>
                <ul class="pa">
                    <li class="cp personal_center" onclick="show()">个人中心</li>
                    <li class="cp switch_user" onclick="switchUser()">账号切换</li>
    <!--                 <li class="cp charge_center">充值中心</li>
                    <li class="cp activation_code">激活码兑换</li> -->
                  <!--   <li class="cp exit" onclick="clientExit()">退出</li> -->
                </ul>
                 <div class="user_link f12 clearfix drive_no" >
                    <i class="fl link_icon"></i><span class="fl">设备未连接</span><i class="fl link_icon_sub"></i>
                 </div>
               <!--  <div class="icon_con">
                    <ol>
                        <li class="icon fl cp game_icon href" data-link="/vrhelp/game"></li>
                        <li class="icon fl cp video_icon href" data-link="/vrhelp/video"></li>
                        <li class="icon fl cp friends_icon href" data-link="/vrhelp/index"></li>
                        <li class="icon fl cp charge_icon href" data-link="/vrhelp/index"></li>
                        <li class="icon fl cp editor_icon" onclick="show()"></li>
                    </ol>
                </div> -->
            </div>
        </div>
        <div class="left_item_nav">
            <ul>
                <li class="pr cp href" data-link="/vrhelp/home"><i class="icon home_icon pa"></i>游戏大厅</li>
                <li class="pr">
                    <h3 class="pr cp">
                        <i class="icon game_icon pa"></i>游戏
                        <i class="icon white_up_icon pa"></i>
                    </h3>
                    <ol class="" style="display: block">
                        <li class="cp href" data-link="/vrhelp/game">游戏库</li>
                        <li class="cp href" data-link="/vrhelp/3dvr">VR玩游戏<i class="icon hot_icon"></i></li>
                    </ol>
                </li>
                <li class="pr cp href" data-link="/vrhelp/video">
                    <i class="icon pa video_icon"></i>
                    3D播播
                </li>
                <li class="pr cp href" data-link="/vrhelp/download">
                    <div class="rotate pa"></div>
                    <i class="icon download_icon pa"></i>
                    下载管理
                </li>
                <li class="pr">
                    <h3 class="pr  cp">
                        <i class="icon time_icon pa"></i>
                        最近游戏
                        <i class="icon white_up_icon pa"></i>
                    </h3>
                    <ol class="game_history"  style="display: block">
                       <!--  <li class="cp  game_list">反击部队<i class="down_state fr tac unDownload"><b class="in_pro"></b><em class="pa">未下载</em></i></i></i></li>
                        <li class="cp  game_list">反击部队<i class="down_state fr tac  hasInstalled"><b class="in_pro"></b><em class="pa">已安装</em></i></i></i></li>
                        <li class="cp  game_list">反击部队<i class="down_state fr tac  unUpdate"><b class="in_pro"></b><em class="pa">未更新</em></i></i></li>
                        <li class="cp  game_list">反击部队<i class="down_state fr tac  downloading pr"><b class="in_pro pa"></b><em class="pa">50%</em></i></li> -->
                    </ol>
                </li>
            </ul>
        </div>
    </div>
    <div class="detail_con mask pr ucenter"><div class="in_detail_con pa pr clearfix"><div class="icon blue_close pa cp"></div><iframe class="framebox" src=""  scrolling="no"></iframe></div></div>
    <iframe src="" frameborder="0" scrolling="no" id="myIframe"  style="width:100%;min-height: 700px"></iframe></div>
@endsection




@section('javascript')
<script src="{{ URL::asset('js/vrGameDetail.js?v=06103') }}"></script>
<script src="{{ URL::asset('js/videoPlay.js') }}"></script>
<script src="{{ URL::asset('js/vrhelp_comment.js') }}"></script>
<script type="text/javascript">
var game_history = {!! $history !!};
var config={
  uid:'{{ $user['uid'] }}',
  face:'{{ $user['face'] }}',
}
var rightFrame = document.getElementById('myIframe').contentWindow;
 $(function(){

        vrGameDetail.init(config);
        videoPlay.init(config);

        var cur_link = vrdb.get('iframe-link');
        if(typeof(cur_link)=="undefined") {
            cur_link = '/vrhelp/home';
        }
        $('#myIframe').attr('src',cur_link);
        $('.left_item_nav').find('li.href').each(function(){
            if($(this).attr('data-link') == cur_link) {
                $(this).addClass('cur');
            }
        })
       $('#myIframe').load(function(){
            setIframeHeight($(this)[0])
       })

        $('.user_name_con').on('click','p',function(){
            $(this).find('.triangle').toggleClass('cur');
            $(this).parents('.user_name_con').find('ul').toggle();
        })
        $('li.href').click(function(){
            var link =  $(this).attr('data-link');
            $('#myIframe').attr('src',link);
            $('.left_item_nav').find('li.href').removeClass('cur');
             $(this).addClass('cur');
             vrdb.set('iframe-link',link);
        });
        //点击收缩
        $('.left_item_nav h3').click(function(){
            $(this).find('.white_up_icon').toggleClass('white_down_icon');
            $(this).next('ol').toggle();
        })
         $('.mask  .close_mask').click(function(){
            $(this).parents('.mask').hide();
           //alert(2)
        })
        //个人中心
        $('.ucenter .blue_close').click(function() {
            toggleUserCenter(false)
        })
        $(document).mouseup(function(e) {
            var  pop = $('.ucenter');
            if(pop.is(e.target)) {
                toggleUserCenter(false)
            }
        });
        $(document).on('click','.game_history li',function() {
            var gameId = $(this).attr('data-val')
             vrGameDetail.checkDetail('game',gameId);
        });
        $('.user_link').click(function(){
            drive.go();
        })
        loadGameHistory();
 })

 window.addEventListener('message', function(event){
   if(typeof(event.data)=="object") {
    var res = event.data
    if(typeof(res.tp)!="undefined") {
        switch (res.tp) {
            case "game_status":
                console.log(res.data)
                vrGameDetail.updateDownLoad(res.data)
            break;
            case "show_drive_title":
                //showDrive(res.data);
            break;
            case "drive_progress":
                drive.installing(res.data)
            break;
            case "drive_change":
                console.log(res.data)
                if(typeof(res.data.state)!="undefined") {
                    if(res.data.deviceType==-1) {
                        drive.out()
                    } else {
                        drive.in(res.data)
                    }
                }
            break;
            case "drive_install_fail":
                 drive.fail()
            break;
            case "history":
               window.history.go(-1);
            break;
            case "reload":
                rightFrame.location.reload(true);
            break;

        }
    }
   }
}, false);


function setIframeHeight(iframe) {
    iframe.height=0;
    if (iframe) {
        var iframeWin = iframe.contentWindow || iframe.contentDocument.parentWindow;
        if (iframeWin.document.body) {
            iframe.height = iframeWin.document.documentElement.scrollHeight || iframeWin.document.body.scrollHeight;
        }
    }
}

function tickGame() {
    vrGameDetail.tickGame();
}

function gameDetail(id) {
     vrGameDetail.checkDetail('game',id);
}

function videoDetail(id) {
    videoPlay.checkVideo('video',id);
}
function show() {
    var src = $(".ucenter .framebox").attr('src');
    if (src.length<1) {
         $(".ucenter .framebox").attr('src','http://www.vronline.com/vrhelp/user');
    }
    toggleUserCenter(true)
}

function toggleUserCenter(show) {
    if(show==false) {
        $('body').removeClass("dimmed")
        $(".ucenter").hide();
    } else {
        $('body').addClass("dimmed")
        $(".ucenter").show();
    }
}

function clientExit() {
    clientCall('mainframe', 'close');
}

function switchUser() {
    console.log('switch user');
    clientCall('loginframe', 'logout');
}

var drive = {
    tp:['','DPVR E2','Oculus DK2','Oculus Rift','HTC VIVE','OSVR','DPVR E3'],
    deviceType:0,
    status:'no',
    in:function(obj){
        this.deviceType = obj.deviceType;
        if(obj.state==0) {
            this.changeLeft('drive_con');
            this.status = 'install';
        } else if(obj.state==1) {
            this.changeLeft('drive_suc');
            this.status = 'complete';
        }
    },
    out:function(){
        this.changeLeft('drive_no');

        this.status = 'out';
    },
    fail:function(){
        this.changeLeft('drive_fail');
        this.status = 'fail';
        rightFrame.postMessage({tp:"drive_install_fail"},'*');
    },
    go:function() {
        if(this.deviceType>0) {
            $('.left_item_nav').find('li.href').removeClass('cur');
            $('#myIframe').attr('src','/vrhelp/drive/'+this.deviceType);
        }
    },
    installing(obj) {
        if(this.status!='install') {
            this.changeLeft('drive_con');
            this.status = 'install';
        }
        if(obj.pro==100) {
            this.changeLeft('drive_suc');
        }
        rightFrame.postMessage({tp:"drive_progress",data:obj},'*');
    },
    reinstall:function(){
        this.changeLeft('drive_con');
        clientCall('driverframe','startinstall','');
    },
    getStatus:function(){
        return this.status
    },
    changeLeft:function(val) {
        var that = $('.user_link')
        var classes = that.attr('class');
        var curClass = classes.substr(classes.indexOf('drive_'));
        if(curClass!=val) {
            if(val=='drive_no') {
                that.find('span').text('设备未连接');
            } else {
                that.find('span').text(this.tp[this.deviceType]);
            }
            that.removeClass(curClass);
            that.addClass(val);
        }
    }
}

function leftCall(a,b,c) {
    if(a=='gameframe' && b=='gamefuncclicked') {
        $.post("/vrhelp/gamehistory/add",c,function(res){
            if(res.code==0) {
                game_history = res.data
                loadGameHistory()
            }
        },'json')
    }
    return clientCall(a,b,c)
}

function loadGameHistory() {
     var history_html = '';
    for(var i=0;i<game_history.length;i++) {
        history_html = history_html+'<li class="cp game_list" data-val="'+game_history[i].appid+'" title="'+game_history[i].name+'"><label>'+game_history[i].name+'</label><i class="down_state fr tac unDownload"><b class="in_pro"></b><em class="pa">未下载</em></i></i></i></li>'
    }
    if(history_html.length>0) {
        $('.game_history').html(history_html);
    }
}
</script>
@endsection
