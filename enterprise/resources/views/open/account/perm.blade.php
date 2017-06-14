@inject('blade', 'App\Helper\BladeHelper')
@extends('open.nav')
@section('content')
<!--内容-->




   <div class="fl jurisdiction">
        <!--选择游戏-->
        <div class="clearfix">
            <div class="fl account_number">
                <ul class="perm-accounts">
                    @if(count($accounts)<1)
                      <li>暂无数据</li>
                    @else
                        @foreach($accounts as $account)
                        <li class="perm-account" data-id="{{ $account['uid'] }}"><span>{{ $account['contacts'] }}</span></li>
                        @endforeach
                    @endif
                </ul>
            </div>
            <div class="fl games">
                <h4>选择游戏</h4>
                <ul>
                    @if($games->total()<1)
                      <li>暂无数据</li>
                    @else
                        @foreach($games as $game)
                        <li class="clearfix perm-game" id="game-{{ $game["appid"] }}">
                           <label><span class="fl game_name">{{ $game["name"] }}</span></label>
                            <div class="fl clearfix">
                                <p class="fl"><label><input type="checkbox" value="1" /><span>编辑配置</span></label></p>
                                <p class="fl"><label><input type="checkbox" value="2" /><span>查看数据</span></label></p>
                            </div>
                        </li>
                        @endforeach
                    @endif
                </ul>
                <span class="preservation btn-save-perm">保存</span>
            </div>
        </div>
   </div>
@endsection


@section('javascript')
<script type="text/javascript">
var curUid=0;
$(function(){
    $(".btn-save-perm").click(function() {
        var games = $(".perm-game");
        var perms = {};
        if(games.length>0) {
            $.each(games,function(a,b){
                var gameid = $(b).attr('id').replace("game-","");
                var cks = $(b).find('input[type="checkbox"]:checked');
                $.each(cks,function(c,d) {
                    var v = parseInt($(d).val());
                    if(typeof(perms[gameid])=="undefined") {
                         perms[gameid] = [];
                    }
                    perms[gameid].push(v);
                });
             });
            if(curUid>0) {
                $.post('/addperms',{uid:curUid,perms:JSON.stringify(perms)},function(res){
                    if(res.code==0) {
                         Open.showMessage('保存成功');
                     } else {
                         Open.showMessage(res.msg);
                     }

                },"json");
            }
        }
    });

    var obj = $(".perm-accounts .perm-account:first");
    loadPerm(obj);

    $(".perm-account").click(function() {
        loadPerm($(this));
    })
});

function loadPerm(obj) {
    if(obj.length<1) {
        return false;
    }
    $("input[type=checkbox]").prop("checked",false);
    obj.addClass("cur").siblings().removeClass('cur');
    var id = obj.attr('data-id');
    curUid = id;
    $.post('/getSonPerms',{uid:id},function(res){
        if(res.code==1) {
            Open.showMessage(res.msg);
        } else {
            if(typeof(res.data.perms)=="object") {
                $.each(res.data.perms,function(gameid,perm) {
                    for(var i=0;i<perm.length;i++) {
                        $("#game-"+gameid).find("input[value="+perm[i]+"]").prop("checked",true);
                    }
                });
            }
        }
    },"json")
}
</script>
@endsection