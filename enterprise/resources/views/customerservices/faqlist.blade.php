@extends('news.layout')

@section('meta')
<title>VRonline客服中心</title>
@endsection

@section("head")
<link rel="stylesheet" href="{{static_res("/news/style/customer.css")}}" />
@endsection

@section('content')
<div class="whole all_con clearfix">
<div class="crumbs clearfix" style="margin-bottom:20px">
            <a class="fl" href="/customer/service">客服中心</a>
            <span class="fl">&gt;</span>
            <a class="fl" href="#">自助查询</a>
        </div>
    <div class="fl left">
        <ul>
            @foreach($faqTps as $faqTp)
            <li class="@if($faqTp['id']==$tp) cur @endif">
                <a class="clearfix" href="/customer/service/faq/{{ $faqTp['id'] }}">
                    <i class="fl {{ $faqTp["icon"] }}"></i>
                    <span>{{ $faqTp["name"] }}</span>
                    <i class="fr"></i>
                </a>
            </li>
            @endforeach
        </ul>
    </div>
    <div class="fl right_show">
        <!--账号问题-->
        <div class="show_con cur">
            @if(isset($faqinfo))
            <ul class="show_msg">
            <li>
                 <div class="show_msg_title">
                        <p class="clearfix">
                            <span class="fl box"></span>
                            <span class="fl text">{{ $faqinfo['question'] }}</span>
                            <span class="fl line"></span>
                            <span class="fr search">已经有{{ $faqinfo['view'] }}人查看</span>
                        </p>
                </div>
                <div class="show_msg_content" style="display:block">{!! $faqinfo['answer'] !!}</div>
            </li>
            </ul>
            @elseif(isset($data))
            <ul class="show_msg">
                @if($data->total()<1)
                 <li>
                 <div class="show_msg_title">
                        <p class="clearfix">
                            <span class="fl box"></span>
                            <span class="fl text"> 暂无数据</span>
                            <span class="fl line"></span>
                            <span class="fr search"></span>
                        </p>
                    </div>
                 </li>
                @else
                    @foreach($data as $faq)
                    <li class="faqinfo" data-id="{{ $faq['id'] }}">
                        <div class="show_msg_title">
                            <p class="clearfix">
                                <span class="fl box"></span>
                                <span class="fl text">{{ $faq['question'] }}</span>
                                <span class="fl line"></span>
                                <span class="fr search">已经有{{ $faq['view'] }}人查看</span>
                            </p>
                        </div>
                    </li>
                    @endforeach
                @endif
            </ul>
           <div class="page_con">{!! $data->render() !!}</div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('javascript')
<script type="text/javascript">
var curid = 0;
    $(".faqinfo").click(function(){
        var id = $(this).attr('data-id');
        if(curid==id) {
            return;
        }
        var that = $(this);
        that.siblings().find(".show_msg_content").remove();
        $.post("/customer/service/faqpost/"+id,function(res){
            if(res.code==0) {
                curid = id;
                that.append(res.data.html);
                that.find(".show_msg_content").fadeIn();
            }
        },"json");
    })
</script>
@endsection