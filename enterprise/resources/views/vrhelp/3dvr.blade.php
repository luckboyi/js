@extends('vrhelp.layout')
@section('meta')
<title></title>
@endsection


@section('content')
 <div class="main_con">
            <div class="vr_playgame">
                <ul class="in_vr_playgame clearfix">
                    <li class="wow_game fl cp pr">
                        <div class="btn_con pa tac">
                            <div class="clearfix">
                                <span class="fl f16">启动游戏</span>
                                <span class="fl f26" onclick="addLocalGame()">+</span>
                            </div>
                        </div>
                    </li>
                    <li class="ow_game fl cp pr">
                        <div class="btn_con pa tac">
                            <div class="clearfix">
                                <span class="fl f16">启动游戏</span>
                                <span class="fl f26" onclick="addLocalGame()">+</span>
                            </div>
                        </div>
                    </li>
                    <li class="lie_game fl cd pr">
                        <div class="btn_con pa tac">
                            <div class="clearfix">
                                <span class="fl f16  disabledCol">启动游戏</span>
                                <span class="fl f26" onclick="addLocalGame()">+</span>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
    <div class="success_add_game_mask mask  pr">
        <div class="add_game_con pa">
            <h4 class=" pr f16">手动添加游戏 <i class="close_mask pa icon cp"></i></h4>
            <div class="body_con tac pr">
                <div class="has_add_con pr f16"><i class="success_add_icon icon pa"></i>游戏已添加</div>
            </div>
            <div class="btn_con tac pa">
                <ul class="">
                    <li class="fl cp">确定</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="vrgame_tips  mask pr ">
        <div class="in_vrgame_tips in_vrgame_tips1 pa pr cur">
            <div class="next_btn pa cp"></div>
        </div>
        <div class="in_vrgame_tips in_vrgame_tips2 pa pr ">
            <div class="next_btn pa cp"></div>
        </div>
         <div class="in_vrgame_tips in_vrgame_tips3 pa pr ">
            <div class="next_btn pa cp last_next_btn"></div>
        </div>
    </div>
@endsection





@section('javascript')
<script type="text/javascript">

function addLocalGame() {
     clientCall('mainframe', 'addlocalgame');
}


$('body').css('background','rgba(255, 255, 255, 0)');

</script>
@endsection
