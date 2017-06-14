@extends('pagegame.detailTmp.detail')
@section('detail_top_logo')
                <a href="javascript:;">
                    <img src="{{$images['slogo']}}" />
                </a>
@endsection
@section('role_intro')
            <div class="nav clearfix">
                <div class="fl part clearfix cur">
                    <div class="fl cur clearfix pr">
                        <i class="fl warrior"></i>
                        <p class="fr">
                            <span class="f18">战士</span>
                            <span class="red">WARRIORS</span>
                        </p>
                    </div>
                    <div class="fl clearfix pr">
                        <i class="fl master"></i>
                        <p class="fr">
                            <span class="f18">法师</span>
                            <span class="red aleft blue">MAGE</span>
                        </p>
                    </div>
                    <div class="fl clearfix pr">
                        <i class="fl taoist"></i>
                        <p class="fr">
                            <span class="f18">道士</span>
                            <span class="red aleft violet">TAOIST</span>
                        </p>
                    </div>
                </div>
                <!-- <a class="fr clearfix" href="javascript:;"><span class="fl f14">更多</span><span class="fr">+</span></a> -->
            </div>
            <div class="newsCon show">
                <img class="role_bg" src="//pic.vronline.com/webgames/images/02.png" />
                <p class="fl character">
                    <span>战士</span>
                    <span>WARRIORS</span>
                </p>
                <div class="fl describe">远程魔法全能型，拥有持续伤害能力，和特 殊的召唤神兽技能，还拥有治疗和增强防御 等辅助手段，大型战斗决不可缺少的职业。</div>
            </div>
            <div class="newsCon">
                <img class="role_bg" src="//pic.vronline.com/webgames/images/img.png" />
                <p class="fl character">
                    <span class="blue">法师</span>
                    <span>WARRIORS</span>
                </p>
                <div class="fl describe">远程魔法全能型，拥有持续伤害能力，和特 殊的召唤神兽技能，还拥有治疗和增强防御 等辅助手段，大型战斗决不可缺少的职业。</div>
            </div>
            <div class="newsCon">
                <img class="role_bg" src="//pic.vronline.com/pic/webgames/images/02.png" />
                <p class="fl character">
                    <span class="violet">道士</span>
                    <span>WARRIORS</span>
                </p>
                <div class="fl describe">远程魔法全能型，拥有持续伤害能力，和特 殊的召唤神兽技能，还拥有治疗和增强防御 等辅助手段，大型战斗决不可缺少的职业。</div>
            </div>
@endsection
@section('custom_center')
            <div class="clearfix">
                <div class="fl">
                    <p><span>传真电话：</span><span>021-54310366</span></p>
                    <p><span>客服电话：</span><span>021-54310366</span></p>
                    <p class="clearfix"><span class="fl">游戏咨询：</span><i class="fl">在线客服</i></p>
                    <p class="clearfix"><span class="fl">充值咨询：</span><i class="fl">在线客服</i></p>
                </div>
                <div class="fr">
                    <span><img src="//pic.vronline.com/webgames/images/kefu.jpg" title="请扫描二维码" /></span>
                    <p>扫二维码</p>
                </div>
            </div>
@endsection
