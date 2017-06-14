@extends('vronline.layout')
@section('meta')
<title>VRonline-vr虚拟现实第一门户网站- {{ $article['article_title'] }}</title>
@endsection

@section("head")

<link href="{{ static_res('/vronline/style/information.css') }}" rel="stylesheet" type="text/css" />
<script src="{{ static_res('/assets/loi/message.js') }}"></script>
<script src="{{ static_res('/vronline/js/comment.js') }}"></script>
<script src="{{ static_res('/vronline/js/vronline.js') }}"></script>
@endsection

@section('content')
<!-- 新闻内页头 -->
<div class="clearfix mb60">
            <div class="news-wrap w_1200">
                <div class="arrow hy-arrow">
                    <a href="/vronline/index">首页</a><span>&gt;</span></span><a href="/vronline/{{ $categoryLink }}/list/{{ $category['id'] }} ">{{ $category['name'] }}</a><span>&gt;</span>{{ $article['article_title'] }}
                </div>
                <div class="left-wrap">
                <!-- 新闻正文 -->
                    <div class="content-wrap">
                        <h1> {{ $article['article_title'] }}</h1>
                        <div class="info clearfix mb20 pb10">
                            <div class="info_tag fl">
                                <span class="pub-time">{{ $updateTime }}</span>
                                <span class="pub-from">来源：{{ $article['article_source'] }}</span><span class="pub-from ml10">作者：<span class="author_name" data-val="{{ $article['article_author_id'] }}"></span>
                            </div>
                            <div class="info_tag fr">
                                <span class="review"><a href="#comment">我要评论</a></span>
                            </div>
                        </div>
                        <div class="content">
                          {!! $article['article_content'] !!}
                        </div>
                    </div>
                    <!--相关文章-->
                    <div class="other-list">
                        <div class="h2-contain">
                            <h2>相关文章</h2>
                        </div>
                        <ul class="list">
                        @foreach($relatedArticles as $relatedArticle)
                            <li>
                            <a href="/vronline/article/detail/{{ $relatedArticle['itemid'] }}" target="_blank">
                                <img src="{{ static_image($relatedArticle['cover']) }}" alt="{{ $relatedArticle['title'] }}">
                                <span class="tit" title="{{ $relatedArticle['title'] }}">{{ $relatedArticle['title'] }}</span>
                            </a>
                            </li>
                        @endforeach
                        </ul>
                    </div>
                      <!--评论区-->

                    <div class="game_resource_commend_con">
                    <a name="comment">
                        <div class="add_word">
                            <p class="title">我有话说：</p>
                            <textarea placeholder="我有话要说......" class="words" id="words" name="txb_Content0"></textarea>
                            <input type="button" value="评论" id="btn_commentadd" class="send"  name="send" data-id="0" group="g0" data-qpid="0" data-qid="0"><a id="zxpl"></a>
                        </div>
                        <div class="comment mt20">
                          <h2 class="comment_heading">
                              <span class="title">最新评论</span>
                          </h2>
                          <div id="comment_con">
                            <div id="in_comment_con"></div>
                            <div class="comment commMore2" id="load_more" style="display:none;"><a class="commMoreA"  href="javascript:;">加载更多</a></div>
                          </div>
                        </div>
                    </div>
                </div>
                <div class="right-wrap">
                    <!--作者栏-->
                    <div class="author-area">
                    <div class="author-info">
                        <div class="img-wrap">
                            <img class="author_cover"  src="https://image.vronline.com/newsimg/1/6556dc3a75962a2ccdba462fae092efd1491393581882.jpg" data-val="{{ $article['article_author_id'] }}">
                        </div>
                        <div class="info-wrap">
                        <p class="tit author_name" data-val="{{ $article['article_author_id'] }}">
                            </p>
                            <p class="des author_intro" data-val="{{ $article['article_author_id'] }}">
                            </p>
                            <p class="info-artical">
                                <span>文章：{{ $authorArticleNums }}</span>
                            </p>
                        </div>
                    </div>
                    <div class="article-tit">最近文章</div>
                    <ul class="article-list">
                                @foreach($authorArticles as $authorArticle)
                                <li>
                                    <a href="/vronline/article/detail/{{ $authorArticle['itemid'] }}" title="{{ $authorArticle['title'] }}" target="_blank">
                                        {{ $authorArticle['title'] }}
                                    </a>
                                </li>
                                @endforeach
                    </ul>
                    <a href="/vronline/author/{{ $article['article_author_id'] }}" class="article-more" target="_blank">阅读更多文章</a>
                    </div>
                    <div class="article-rightAd">
                    </div>
                    <div class="h2-wrap">
                        <h2>新闻排行</h2>
                    </div>
                    <ul class="hot-list">
                    @foreach($tops['news-rank'] as $key=>$top)
                        <li>
                            <a href="{{ $top['target_url'] }}"" target="_blank">
                                <em class="num num3">{{ $key+1 }}</em>
                                <span class="tit">{{ $top['title'] }}</span>
                                <span class="img-wrap">
                                    <img src="{{ static_image($top['cover']) }}" alt="{{ $top['title'] }}">
                                </span>

                            </a>
                        </li>
                    @endforeach
                    </ul>
                </div>
            </div>
        </div>
@endsection

@section('javascript')
<script type="text/javascript">
    $(function(){
        $(".hot-list li:first").addClass('current');
        $(".hot-list li").hover(function(){
            $(this).addClass('current').siblings().removeClass('current');
        });
    })

    // 评论
    Comment.init({
        userid:'{{$uid}}',
        target_id:'{{$article_id}}',
        type:'news_news',
    });
    // 统计pv
    statPV('news_news', '{{$article_id}}');
</script>
@endsection
