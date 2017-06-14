@extends('vronline.layout')
@section('meta')
<title>VRonline - vr虚拟现实第一门户网站 - VRonline.com</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{ static_res('/vronline/style/vrhome.css') }}">
@endsection

@section('content')
	<div class="container">
    @if(isset($recommends['topbanner'][0]['cover']) && $recommends['topbanner'][0]['cover'])
		<img class="topImg" src="{{ static_image($recommends['topbanner'][0]['cover']) }}" />
    @else
		<img class="topImg" src="{{ static_res('/vronline/images/topimg.png') }}" />
    @endif
		<div class="vrhome">
			<!--vronline推荐-->
			<div class="recommend">
				<div class="name clearfix">
                @if(isset($pos['topgame0']['pos_name']) && $pos['topgame0']['pos_name'] && isset($recommends['topgame0']) && $recommends['topgame0'] && is_array($recommends['topgame0']))
					<span class="fl pr f16 cur">{{$pos['topgame0']['pos_name']}}</span>
                @endif
                @if(isset($pos['topgame1']['pos_name']) && $pos['topgame1']['pos_name'] && isset($recommends['topgame1']) && $recommends['topgame1'] && is_array($recommends['topgame1']))
					<span class="fl pr f16">{{$pos['topgame1']['pos_name']}}</span>
                @endif
                @if(isset($pos['topgame2']['pos_name']) && $pos['topgame2']['pos_name'] && isset($recommends['topgame2']) && $recommends['topgame2'] && is_array($recommends['topgame2']))
					<span class="fl pr f16">{{$pos['topgame2']['pos_name']}}</span>
                @endif
                @if(isset($pos['topgame3']['pos_name']) && $pos['topgame3']['pos_name'] && isset($recommends['topgame3']) && $recommends['topgame3'] && is_array($recommends['topgame3']))
					<span class="fl pr f16">{{$pos['topgame3']['pos_name']}}</span>
                @endif
				</div>
				<div class="recommendCon">
					<ul class="clearfix cur">
                    @if(isset($recommends['topgame0']) && $recommends['topgame0'] && is_array($recommends['topgame0']))
                    @for($i = 0; $i < count($recommends['topgame0']); $i++)
						<li class="fl">
							<a class="pr" href="{{$recommends['topgame0'][$i]['target_url']}}" target="_blank">
								<img src="{{ static_image($recommends['topgame0'][$i]['cover'],'1-222-288')}}" />
								<p>{{$recommends['topgame0'][$i]['title']}}</p>
                                @if(isset($recommends['topgame0'][$i]['category'][0]) && $recommends['topgame0'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['topgame0'][$i]['category'][0].".name"))
								<p class="type">{{config("category.vronline_game_class.".$recommends['topgame0'][$i]['category'][0].".name")}}</p>
                                @endif
								<div class="mask pa"></div>
							</a>
						</li>
                    @endfor
                    @endif
					</ul>
					<ul class="clearfix">
                    @if(isset($recommends['topgame1']) && $recommends['topgame1'] && is_array($recommends['topgame1']))
                    @for($i = 0; $i < count($recommends['topgame1']); $i++)
						<li class="fl">
							<a class="pr" href="{{$recommends['topgame1'][$i]['target_url']}}" target="_blank">
								<img src="{{ static_image($recommends['topgame1'][$i]['cover'],'1-222-288')}}" />
								<p>{{$recommends['topgame1'][$i]['title']}}</p>
                                @if(isset($recommends['topgame1'][$i]['category'][0]) && $recommends['topgame1'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['topgame1'][$i]['category'][0].".name"))
								<p class="type">{{config("category.vronline_game_class.".$recommends['topgame1'][$i]['category'][0].".name")}}</p>
                                @endif
								<div class="mask pa"></div>
							</a>
						</li>
                    @endfor
                    @endif
					</ul>
					<ul class="clearfix">
                    @if(isset($recommends['topgame2']) && $recommends['topgame2'] && is_array($recommends['topgame2']))
                    @for($i = 0; $i < count($recommends['topgame2']); $i++)
						<li class="fl">
							<a class="pr" href="{{$recommends['topgame2'][$i]['target_url']}}" target="_blank">
								<img src="{{ static_image($recommends['topgame2'][$i]['cover'],'1-222-288')}}" />
								<p>{{$recommends['topgame2'][$i]['title']}}</p>
                                @if(isset($recommends['topgame2'][$i]['category'][0]) && $recommends['topgame2'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['topgame2'][$i]['category'][0].".name"))
								<p class="type">{{config("category.vronline_game_class.".$recommends['topgame2'][$i]['category'][0].".name")}}</p>
                                @endif
								<div class="mask pa"></div>
							</a>
						</li>
                    @endfor
                    @endif
					</ul>
					<ul class="clearfix">
                    @if(isset($recommends['topgame3']) && $recommends['topgame3'] && is_array($recommends['topgame3']))
                    @for($i = 0; $i < count($recommends['topgame3']); $i++)
						<li class="fl">
							<a class="pr" href="{{$recommends['topgame3'][$i]['target_url']}}" target="_blank">
								<img src="{{ static_image($recommends['topgame3'][$i]['cover'],'1-222-288')}}" />
								<p>{{$recommends['topgame3'][$i]['title']}}</p>
                                @if(isset($recommends['topgame3'][$i]['category'][0]) && $recommends['topgame3'][$i]['category'][0] && config("category.vronline_game_class.".$recommends['topgame3'][$i]['category'][0].".name"))
								<p class="type">{{config("category.vronline_game_class.".$recommends['topgame3'][$i]['category'][0].".name")}}</p>
                                @endif
								<div class="mask pa"></div>
							</a>
						</li>
                    @endfor
                    @endif
					</ul>
				</div>
			</div>
			<!---->
			<div class="vrnews clearfix">
				<div class="fl left">
					<div class="tab pr">
						<i class="pa prev"></i>
						<div class="carousel pr">
							<ul class="clearfix">
                        @if(isset($recommends['topleftslider']) && $recommends['topleftslider'] && is_array($recommends['topleftslider']))
                            @for($i = 0; $i < count($recommends['topleftslider']); $i++)
								<li class="fl"><a href="{{$recommends['topleftslider'][$i]['target_url']}}" target="_blank"><img src="{{ static_image($recommends['topleftslider'][$i]['cover'],'1-433-454') }}" ></a></li>
                            @endfor
                        @endif
							</ul>
						</div>
						<p class="pa num"><span class="f18 indexNum">1</span><span class="f18">/</span><span class="f18 total">3</span></p>
						<i class="pa next"></i>
					</div>
					<div class="video">
                        @if(isset($pos['gamevideo']) && isset($pos['gamevideo']['pos_name']) && $pos['gamevideo']['pos_name'])
						    <p class="til">{{$pos['gamevideo']['pos_name']}}</p>
                        @else
                            <p class="til">游戏视频</p>
                        @endif
						<div class="clearfix">
                        @if(isset($recommends['companylogo'][0]['cover']) && $recommends['companylogo'][0]['cover'])
							<a href="{{$recommends['companylogo'][0]['target_url']}}" class="fl logo"><img src="{{ static_image($recommends['companylogo'][0]['cover']) }}" ></img></a>
                        @endif
                        @if(isset($recommends['gamevideo'][0]) && $recommends['gamevideo'][0] && is_array($recommends['gamevideo'][0]))
							<a class="fl pr" href="{{$recommends['gamevideo'][0]['target_url']}}" target="_blank">
								<img src="{{ static_image($recommends['gamevideo'][0]['cover']) }}" />
								<div class="pa mask"></div>
							</a>
                        @endif
						</div>
					</div>
				</div>
				<div class="fl middle">
                @if(isset($recommends['middlenews'][0]) && $recommends['middlenews'][0])
					<a class="hot" href="{{$recommends['middlenews'][0]['target_url']}}" target="_blank">
						<p class="clearfix">
							<span class="fl type">评测</span>
							<span class="f16 fl ells">{{$recommends['middlenews'][0]['title']}}</span>
						</p>
						<div class="clearfix">
							<img class="fl" src="{{ static_image($recommends['middlenews'][0]['cover']) }}" />
							<p class="fl">{{strip_tags($recommends['middlenews'][0]['title'])}}</p>
						</div>
					</a>
                @endif
					<ul>
                    @if($news && is_array($news))
                      @for($i = 0; $i < count($news) && $i <= 3; $i++)
						<li class="clearfix">
							<a href="/vronline/article/detail/{{$news[$i]['itemid']}}" target="_blank">
								<span class="fl">资讯</span>
								<p class="fl ells">{{$news[$i]['title']}}</p>
							</a>
						</li>
                      @endfor
                    @endif
                    @if(isset($recommends['middlenews'][1]) && $recommends['middlenews'][1])
						<li class="clearfix recom">
							<a href="{{$recommends['middlenews'][1]['target_url']}}" target="_blank">
								<span class="fl">推荐</span>
								<p class="fl ells">{{$recommends['middlenews'][1]['title']}}</p>
							</a>
						</li>
                    @endif
                    @if($news && is_array($news))
                      @for($i = 4; $i < count($news) && $i <= 7; $i++)
						<li class="clearfix">
							<a href="/vronline/article/detail/{{$news[$i]['itemid']}}" target="_blank">
								<span class="fl">资讯</span>
								<p class="fl ells">{{$news[$i]['title']}}</p>
							</a>
						</li>
                      @endfor
                    @endif
                    @if(isset($recommends['middlenews'][2]) && $recommends['middlenews'][2])
						<li class="clearfix recom">
							<a href="{{$recommends['middlenews'][2]['target_url']}}" target="_blank">
								<span class="fl">推荐</span>
								<p class="fl ells">{{$recommends['middlenews'][2]['title']}}</p>
							</a>
						</li>
                    @endif
                    @if($news && is_array($news))
                      @for($i = 8; $i < count($news) && $i <= 11; $i++)
						<li class="clearfix">
							<a href="/vronline/article/detail/{{$news[$i]['itemid']}}" target="_blank">
								<span class="fl">资讯</span>
								<p class="fl ells">{{$news[$i]['title']}}</p>
							</a>
						</li>
                      @endfor
                    @endif
					</ul>
					<a href="/vronline/article/list/6" target="_blank"><p class="f14 moreNews">+更多新闻</p></a>
				</div>
				<div class="fl right">
					<div class="clearfix title">
                    @if(isset($pos['toprighttopic']) && isset($pos['toprighttopic']['pos_name']) && $pos['toprighttopic']['pos_name'])
						<h3 class="fl f16">{{$pos['toprighttopic']['pos_name']}}</h3>
                    @else
						<h3 class="fl f16">栏目专题</h3>
                    @endif
						<a class="fr f16 clearfix more" href="/vronline/top/{{$pos['toprighttopic']['pos_code']}}" target="_blank">
							<span class="fl">更多</span>
							<i class="fl icon"></i>
						</a>
					</div>
                @if(isset($recommends['toprighttopic']) && $recommends['toprighttopic'] && is_array($recommends['toprighttopic']))
                    @for($i = 0; $i < count($recommends['toprighttopic']); $i++)
					<div class="special_games">
						<a class="pr" href="{{$recommends['toprighttopic'][$i]['target_url']}}" target="_blank">
							<img class="pa" src="{{ static_image($recommends['toprighttopic'][$i]['cover']) }}" />
							<div class="pa mask"></div>
							<i class="pa icon play"></i>
						</a>
					</div>
                    @endfor
                @endif
					<!--2017-4-5-->
                @if(isset($recommends['rightadv']) && $recommends['rightadv'] && is_array($recommends['rightadv']))
                    @for($i = 0; $i < count($recommends['rightadv']); $i++)
					<a class="pr gamesColumn" href="{{$recommends['rightadv'][$i]['target_url']}}" target="_blank">
						<img src="{{ static_image($recommends['rightadv'][$i]['cover']) }}" >
						<div class="pa mask"></div>
					</a>
                    @endfor
                @endif
				</div>
			</div>
			<!--热门评测-->
			<div class="hot_evaluating">
				<div class="clearfix">
                @if(isset($pos['hotpc']) && isset($pos['hotpc']['pos_name']) && $pos['hotpc']['pos_name'])
					<p class="fl til">{{$pos['hotpc']['pos_name']}}</p>
                @else
					<p class="fl til">热门评测</p>
                @endif
					<a class="fr f16 clearfix more" href="/vronline/pc/list/1" target="_blank">
						<span class="fl">更多</span>
						<i class="fl icon"></i>
					</a>
				</div>
				<div class="evaluating pr">
					<i class="icon prev pa"></i>
					<div class="evaluatingCon">
						<ul class="clearfix">

                        @if(isset($recommends['hotpc']) && $recommends['hotpc'] && is_array($recommends['hotpc']))
                            @for($i = 0; $i < count($recommends['hotpc']); $i++)
							<li class="fl">
								<a class="pr" href="{{$recommends['hotpc'][$i]['target_url']}}" target="_blank">
									<img src="{{ static_image($recommends['hotpc'][$i]['cover'],'1-234-298')}}"/>
									<!--2017-4-5添加蒙版-->
									<div class="pa mask"></div>
									<div class="clearfix">
										<div class="fl bg">
											<p class="clearfix">
												<i class="fl icon message"></i>
												<span class="fl">{{$recommends['hotpc'][$i]['comment']}}</span>
											</p>
										</div>
										<div class="fl bg">
											<p class="clearfix">
												<i class="fl icon playNum"></i>
												<span class="fl">{{$recommends['hotpc'][$i]['view']}}</span>
											</p>
										</div>
									</div>
								</a>
							</li>
                            @endfor
                        @endif
						</ul>
					</div>
					<i class="pa icon next"></i>
				</div>
			</div>
			<!--厂商专区-->
			<div class="manufacturer">
				<div class="clearfix firm">
					<span class="fl f16 pr cur">Oculus专区</span>
					<span class="fl f16 pr">HTC VIVE专区</span>
					<span class="fl f16 pr">PS VR专区</span>
				</div>
				<div class="firmNews cur clearfix">
					<div class="fl firmCon cur">
						<div class="clearfix">
							<div class="fl newEvaluating">
								<div class="clearfix">

									<p class="fl til">最新评测</p>
									<a class="fr f16 clearfix more" href="/vronline/pc/list/1" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>

								</div>
								<!--2017-4-5-->
                            @if(isset($recommends['newpc1'][0]) && $recommends['newpc1'][0] && is_array($recommends['newpc1'][0]))
								<a class="pr" href="{{$recommends['newpc1'][0]['target_url']}}" target="_blank">
									<img src="{{ static_image($recommends['newpc1'][0]['cover']) }}" />
									<p class="pa ells f14">{{$recommends['newpc1'][0]['title']}}</p>
									<span class="pa fraction f18">{{$recommends['newpc1'][0]['score']}}</span>
								</a>
                            @endif
							</div>
							<div class="fl newInformation">
								<div class="clearfix">
									<p class="fl til">最新资讯</p>
									<a class="fr f16 clearfix more" href="/vronline/article" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>
								</div>
								<ul>
                                @if($oculusNews && is_array($oculusNews))
                                  @for($i = 0; $i < count($oculusNews) && $i < 7; $i++)
									<li>
										<a class="clearfix" href="/vronline/article/detail/{{$oculusNews[$i]['itemid']}}" target="_blank">
											<span class="fl ells">{{trim($oculusNews[$i]['title'])}}</span>
											<span class="fr">{{date("m-d", $oculusNews[$i]['time'])}}</span>
										</a>
									</li>
                                  @endfor
                                @endif
								</ul>
							</div>
						</div>
						<div class="gameshow">
							<ul class="clearfix">
                        @if(isset($recommends['bottomnews1']) && $recommends['bottomnews1'] && is_array($recommends['bottomnews1']))
                            @for($i = 0; $i < count($recommends['bottomnews1']); $i++)
								<li class="fl">
									<a href="{{$recommends['bottomnews1'][$i]['target_url']}}" target="_blank">
										<img src="{{ static_image($recommends['bottomnews1'][$i]['cover']) }}"/>
										<p class="f14">{{$recommends['bottomnews1'][$i]['title']}}</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
					<div class="fr newgames rankingList">
						<div class="clearfix">
                        @if(isset($pos['newgame1']) && isset($pos['newgame1']['pos_name']) && $pos['newgame1']['pos_name'])
							<p class="fl til">{{$pos['newgame1']['pos_name']}}</p>
                        @else
							<p class="fl til">新游推荐</p>
                        @endif
							<a class="fr f16 clearfix more" href="/vronline/top/{{$pos['newgame1']['pos_code']}}" target="_blank">
								<span class="fl">更多</span>
								<i class="fl icon"></i>
							</a>
						</div>
						<div>
							<div class="clearfix title">

								<span class="fl ranking">排名</span>
								<span class="fl gameName">名称</span>
								<span class="fl score">评分</span>
								<span class="fr buy">购买</span>

							</div>
							<ul>
                        @if(isset($recommends['newgame1']) && $recommends['newgame1'] && is_array($recommends['newgame1']))
                            @for($j = 0; $j < count($recommends['newgame1']); $j++)
								<li class="@if($j == 0) cur @endif">
									<a href="{{$recommends['newgame1'][$j]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($j == 0) rankingColor @endif">{{$j+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['newgame1'][$j]['title']}}</span>
											<span class="fl score f14">{{$recommends['newgame1'][$j]['score']}}</span>
											<span class="fr buy">购买</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['newgame1'][$j]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
				</div>
				<div class="firmNews clearfix">
					<div class="fl firmCon cur">
						<div class="clearfix">
							<div class="fl newEvaluating">
								<div class="clearfix">
									<p class="fl til">最新评测</p>
									<a class="fr f16 clearfix more" href="/vronline/pc/list/1" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>
								</div>
								<!--2017-4-5-->
                            @if(isset($recommends['newpc2'][0]) && $recommends['newpc2'][0] && is_array($recommends['newpc2'][0]))
								<a class="pr" href="{{$recommends['newpc2'][0]['target_url']}}" target="_blank">
									<img src="{{ static_image($recommends['newpc2'][0]['cover']) }}" />
									<p class="pa ells f14">{{$recommends['newpc2'][0]['title']}}</p>
									<span class="pa fraction f18">{{$recommends['newpc2'][0]['score']}}</span>
								</a>
                            @endif
							</div>
							<div class="fl newInformation">
								<div class="clearfix">
									<p class="fl til">最新资讯</p>
									<a class="fr f16 clearfix more" href="/vronline/article" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>
								</div>
								<ul>
                                @if($htcNews && is_array($htcNews))
                                  @for($i = 0; $i < count($htcNews) && $i < 7; $i++)
									<li>
										<a class="clearfix" href="/vronline/article/detail/{{$htcNews[$i]['itemid']}}" target="_blank">
											<span class="fl ells">{{trim($htcNews[$i]['title'])}}</span>
											<span class="fr">{{date("m-d", $htcNews[$i]['time'])}}</span>
										</a>
									</li>
                                  @endfor
                                @endif
								</ul>
							</div>
						</div>
						<div class="gameshow">
							<ul class="clearfix">
                        @if(isset($recommends['bottomnews2']) && $recommends['bottomnews2'] && is_array($recommends['bottomnews2']))
                            @for($i = 0; $i < count($recommends['bottomnews2']); $i++)
								<li class="fl">
									<a href="{{$recommends['bottomnews2'][$i]['target_url']}}" target="_blank">
										<img src="{{ static_image($recommends['bottomnews2'][$i]['cover']) }}"/>
										<p class="f14">{{$recommends['bottomnews2'][$i]['title']}}</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
					<div class="fr newgames rankingList">
						<div class="clearfix">
                        @if(isset($pos['newgame2']) && isset($pos['newgame2']['pos_name']) && $pos['newgame2']['pos_name'])
							<p class="fl til">{{$pos['newgame2']['pos_name']}}</p>
                        @else
							<p class="fl til">新游推荐</p>
                        @endif
							<a class="fr f16 clearfix more" href="/vronline/top/{{$pos['newgame2']['pos_code']}}" target="_blank">
								<span class="fl">更多</span>
								<i class="fl icon"></i>
							</a>
						</div>
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">名称</span>
								<span class="fl score">评分</span>
								<span class="fr buy">购买</span>
							</div>
							<ul>
                        @if(isset($recommends['newgame2']) && $recommends['newgame2'] && is_array($recommends['newgame2']))
                            @for($jj = 0; $jj < count($recommends['newgame2']); $jj++)
								<li class="@if($jj == 0) cur @endif">
									<a href="{{$recommends['newgame2'][$jj]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($jj == 0) rankingColor @endif">{{$jj+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['newgame2'][$jj]['title']}}</span>
											<span class="fl score f14">{{$recommends['newgame2'][$jj]['score']}}</span>
											<span class="fr buy">购买</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['newgame2'][$jj]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
				</div>
				<div class="firmNews clearfix">
					<div class="fl firmCon cur">
						<div class="clearfix">
							<div class="fl newEvaluating">
								<div class="clearfix">
									<p class="fl til">最新评测</p>
									<a class="fr f16 clearfix more" href="/vronline/pc/list/1" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>
								</div>
								<!--2017-4-5-->
                            @if(isset($recommends['newpc3'][0]) && $recommends['newpc3'][0] && is_array($recommends['newpc3'][0]))
								<a class="pr" href="{{$recommends['newpc3'][0]['target_url']}}" target="_blank">
									<img src="{{ static_image($recommends['newpc3'][0]['cover']) }}" />
									<p class="pa ells f14">{{$recommends['newpc3'][0]['title']}}</p>
									<span class="pa fraction f18">{{$recommends['newpc3'][0]['score']}}</span>
								</a>
                            @endif
							</div>
							<div class="fl newInformation">
								<div class="clearfix">
									<p class="fl til">最新资讯</p>
									<a class="fr f16 clearfix more" href="/vronline/article" target="_blank">
										<span class="fl">更多</span>
										<i class="fl icon"></i>
									</a>
								</div>
								<ul>
                                @if($psvrNews && is_array($psvrNews))
                                  @for($i = 0; $i < count($psvrNews) && $i < 7; $i++)
									<li>
										<a class="clearfix" href="/vronline/article/detail/{{$psvrNews[$i]['itemid']}}" target="_blank">
											<span class="fl ells">{{trim($psvrNews[$i]['title'])}}</span>
											<span class="fr">{{date("m-d", $psvrNews[$i]['time'])}}</span>
										</a>
									</li>
                                  @endfor
                                @endif
								</ul>
							</div>
						</div>
						<div class="gameshow">
							<ul class="clearfix">
                        @if(isset($recommends['bottomnews3']) && $recommends['bottomnews3'] && is_array($recommends['bottomnews3']))
                            @for($i = 0; $i < count($recommends['bottomnews3']); $i++)
								<li class="fl">
									<a href="{{$recommends['bottomnews3'][$i]['target_url']}}" target="_blank">
										<img src="{{ static_image($recommends['bottomnews3'][$i]['cover']) }}"/>
										<p class="f14">{{$recommends['bottomnews3'][$i]['title']}}</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
					<div class="fr newgames rankingList">
						<div class="clearfix">
                        @if(isset($pos['newgame3']) && isset($pos['newgame3']['pos_name']) && $pos['newgame3']['pos_name'])
							<p class="fl til">{{$pos['newgame3']['pos_name']}}</p>
                        @else
							<p class="fl til">新游推荐</p>
                        @endif
							<a class="fr f16 clearfix more" href="/vronline/top/{{$pos['newgame3']['pos_code']}}" target="_blank">
								<span class="fl">更多</span>
								<i class="fl icon"></i>
							</a>
						</div>
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">名称</span>
								<span class="fl score">评分</span>
								<span class="fr buy">购买</span>
							</div>
							<ul>
                        @if(isset($recommends['newgame3']) && $recommends['newgame3'] && is_array($recommends['newgame3']))
                            @for($jjj = 0; $jjj < count($recommends['newgame3']); $jjj++)
								<li class="@if($jjj == 0) cur @endif">
									<a href="{{$recommends['newgame3'][$jjj]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($jjj == 0) rankingColor @endif">{{$jjj+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['newgame3'][$jjj]['title']}}</span>
											<span class="fl score f14">{{$recommends['newgame3'][$jjj]['score']}}</span>
											<span class="fr buy">购买</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['newgame3'][$jjj]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
                        @endif
							</ul>
						</div>
					</div>
				</div>
			</div>
			<!--排行榜-->
			<div class="ranks">
				<div class="clearfix">
                    @if(isset($recommends['rankvrgame']) && $recommends['rankvrgame'] && is_array($recommends['rankvrgame']))
					<div class="fl rankingList">
                    @if(isset($pos['rankvrgame']['pos_name']) && $pos['rankvrgame']['pos_name'] && isset($recommends['rankvrgame']) && $recommends['rankvrgame'] && is_array($recommends['rankvrgame']))
						<p class="til">{{$pos['rankvrgame']['pos_name']}}</p>
                    @else
						<p class="til">热门VR游戏榜</p>
                    @endif
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">游戏</span>
								<span class="fl score">票数</span>
								<span class="fr buy">升降</span>
							</div>
							<ul>
                            @for($i = 0; $i < count($recommends['rankvrgame']); $i++)
                                <?php
if ($recommends['rankvrgame'][$i]['intro'] == "1" || $i == 0) {
    $flag      = "↑";
    $flagclass = "up";
} else if ($recommends['rankvrgame'][$i]['intro'] == "2") {
    $flag      = "↓";
    $flagclass = "down";
} else {
    $flag      = "—";
    $flagclass = "";
}
?>
								<li @if($i == 0) class="cur" @endif>
									<a href="{{$recommends['rankvrgame'][$i]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($i == 0) rankingColor @endif">{{$i+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['rankvrgame'][$i]['title']}}</span>
											<span class="fl score f14">{{$recommends['rankvrgame'][$i]['weight']}}</span>
											<span class="fr {{$flagclass}}">{{$flag}}</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['rankvrgame'][$i]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
							</ul>
						</div>
					</div>
                    @endif
                    @if(isset($recommends['rankvideo']) && $recommends['rankvideo'] && is_array($recommends['rankvideo']))
					<div class="fl rankingList">
                    @if(isset($pos['rankvideo']['pos_name']) && $pos['rankvideo']['pos_name'] && isset($recommends['rankvideo']) && $recommends['rankvideo'] && is_array($recommends['rankvideo']))
						<p class="til">{{$pos['rankvideo']['pos_name']}}</p>
                    @else
						<p class="til">热门视频榜</p>
                    @endif
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">视频</span>
								<span class="fl score">票数</span>
								<span class="fr buy">升降</span>
							</div>
							<ul>
                            @for($i = 0; $i < count($recommends['rankvideo']); $i++)
                                <?php
if ($recommends['rankvideo'][$i]['intro'] == "1" || $i == 0) {
    $flag      = "↑";
    $flagclass = "up";
} else if ($recommends['rankvideo'][$i]['intro'] == "2") {
    $flag      = "↓";
    $flagclass = "down";
} else {
    $flag      = "—";
    $flagclass = "";
}
?>
								<li @if($i == 0) class="cur" @endif>
									<a href="{{$recommends['rankvideo'][$i]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($i == 0) rankingColor @endif">{{$i+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['rankvideo'][$i]['title']}}</span>
											<span class="fl score f14">{{$recommends['rankvideo'][$i]['weight']}}</span>
											<span class="fr {{$flagclass}}">{{$flag}}</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['rankvideo'][$i]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
							</ul>
						</div>
					</div>
                    @endif
                    @if(isset($recommends['bottomrank1']) && $recommends['bottomrank1'] && is_array($recommends['bottomrank1']))
					<div class="fl rankingList">
                    @if(isset($pos['bottomrank1']['pos_name']) && $pos['bottomrank1']['pos_name'] && isset($recommends['bottomrank1']) && $recommends['bottomrank1'] && is_array($recommends['bottomrank1']))
						<p class="til">{{$pos['bottomrank1']['pos_name']}}</p>
                    @else
						<p class="til">VR游戏期待榜</p>
                    @endif
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">游戏</span>
								<span class="fr buy">发售日</span>
							</div>
							<ul>
                            @for($i = 0; $i < count($recommends['bottomrank1']); $i++)
								<li @if($i == 0) class="cur" @endif>
									<a href="{{$recommends['bottomrank1'][$i]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($i == 0) rankingColor @endif">{{$i+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['bottomrank1'][$i]['title']}}</span>
											<span class="fr">{{date("n.j", $recommends['bottomrank1'][$i]['time'])}}</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['bottomrank1'][$i]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
							</ul>
						</div>
					</div>
                    @endif
                    @if(isset($recommends['bottomrank2']) && $recommends['bottomrank2'] && is_array($recommends['bottomrank2']))
					<div class="fl rankingList">
                    @if(isset($pos['bottomrank2']['pos_name']) && $pos['bottomrank2']['pos_name'] && isset($recommends['bottomrank2']) && $recommends['bottomrank2'] && is_array($recommends['bottomrank2']))
						<p class="til">{{$pos['bottomrank2']['pos_name']}}</p>
                    @else
						<p class="til">VR硬件期待榜</p>
                    @endif
						<div>
							<div class="clearfix title">
								<span class="fl ranking">排名</span>
								<span class="fl gameName">硬件</span>
								<span class="fl score"></span>
								<span class="fr buy">升降</span>
							</div>
							<ul>
                            @for($i = 0; $i < count($recommends['bottomrank2']); $i++)
                                <?php
if ($recommends['bottomrank2'][$i]['intro'] == "1" || $i == 0) {
    $flag      = "↑";
    $flagclass = "up";
} else if ($recommends['bottomrank2'][$i]['intro'] == "2") {
    $flag      = "↓";
    $flagclass = "down";
} else {
    $flag      = "—";
    $flagclass = "";
}
?>
								<li @if($i == 0) class="cur" @endif>
									<a href="{{$recommends['bottomrank2'][$i]['target_url']}}" target="_blank">
										<div class="clearfix title">
											<span class="fl ranking @if($i == 0) rankingColor @endif">{{$i+1}}</span>
											<span class="fl gameName ells f14">{{$recommends['bottomrank2'][$i]['title']}}</span>
											<span class="fl score f14"></span>
											<span class="fr {{$flagclass}}">{{$flag}}</span>
										</div>
										<p class="gameImg">
											<img src="{{ static_image($recommends['bottomrank2'][$i]['cover']) }}"/>
										</p>
									</a>
								</li>
                            @endfor
							</ul>
						</div>
					</div>
                    @endif
				</div>
			</div>
		</div>
	</div>
@endsection

@section('javascript')
	<script>
		$(function(){
			//排行榜
			$('.rankingList ul li').hover(function(){
				var i = $(this).index();
				$(this).addClass('cur').siblings().removeClass('cur');
			});
			//tab切换
			$('.name span').click(function(){
				var i = $(this).index();
				$(this).addClass('cur').siblings().removeClass('cur');
				$(this).parents('.recommend').find('ul').eq(i).addClass('cur').siblings().removeClass('cur');
			});
			$('.firm').on('click','span',function(){
				var i = $(this).index();
				$(this).addClass('cur').siblings().removeClass('cur');
				$(this).parents('.manufacturer').find('.firmNews').eq(i).addClass('cur').siblings().removeClass('cur');
			});

			//图片轮播
			var timer=null;
			timer=setInterval(tab,2000);
			var now=1;
			var len=$('.vrnews .tab ul li').length;
			var wid=$('.vrnews .tab ul li').width();
			$('.total').text(len);
			function tab(){
				$(".vrnews .tab ul").animate({marginLeft:-wid},600, function () {
	                $(".vrnews .tab ul>li").eq(0).appendTo($(".vrnews .tab ul"));
	                $(".vrnews .tab ul").css('marginLeft','0px');
	            });
	            now++;
	            if(now==len+1){
	            	now=1;
	            }
	            $('.indexNum').text(now);
	            $('.total').text(len);
			};
			$('.vrnews .tab').hover(function(){
				clearInterval(timer);
			},function(){
				timer=setInterval(tab,2000);
			});
			$('.vrnews .tab .next').click(function () {
	            tab();
	        });
	        $('.vrnews .tab .prev').click(function () {
	            $(".vrnews .tab ul").css('marginLeft',-wid);
	            var leng=len-1;
	            $(".vrnews .tab ul>li").eq(leng).prependTo($(".vrnews .tab ul"));
	            $(".vrnews .tab ul").animate({marginLeft:"0px"},600);
	            now--;
	            if(now<1){
	            	now=len;
	            }
	            $('.indexNum').text(now);
	            $('.total').text(len);
	        });

	        //热门评测轮播
	        function evaluatingTab(){
	        	var timer=null;
				timer=setInterval(hotTab,2000);
				var len=$('.evaluating ul li').length;
				var wid=$('.evaluating ul li').width();
				function hotTab(){
					$(".evaluating ul").animate({marginLeft:-wid},600, function () {
		                $(".evaluating ul>li").eq(0).appendTo($(".evaluating ul"));
		                $(".evaluating ul").css('marginLeft','0px');
		           });
				};
				$('.evaluating').hover(function(){
					clearInterval(timer);
				},function(){
					timer=setInterval(hotTab,2000);
				});
				$('.evaluating .next').click(function () {
		            hotTab();
		        });
		        $('.evaluating .prev').click(function () {
		            $(".evaluating ul").css('marginLeft',-wid);
		            var leng=len-1;
		            $(".evaluating ul>li").eq(leng).prependTo($(".evaluating ul"));
		            $(".evaluating ul").animate({marginLeft:"0px"},600);
		        });
	       };
	       evaluatingTab();
		})
	</script>
@endsection