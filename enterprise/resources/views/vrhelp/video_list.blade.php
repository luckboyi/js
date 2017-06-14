<!DOCTYPE html>
@extends('vrhelp.layout')
@section('meta')
<title>视频列表页</title>

@endsection



@section('content')
<style>
.slideImageBox ul{margin-left: calc(0px - 55%);}
</style>
      <div class="grid main_con">
      <div class="bobo_banner">
      <div class="bobo_slide_list pr" id="bobo_slide_list">

            </div>

     </div>
                     <div class="clf1"><ul>
                       <?php $index = 0;?>
                       @foreach(config("video.class") as $key=>$value)

                         <li data="{!!$value['id']!!}" ><a href='javascript:;'>{{$value["name"]}}</a></li>
                         <?php $index++; if($index==9) break; ?>
                       @endforeach
      </ul></div>
      <div class="v_option">
        <div class="fl f12 tac">
          <span data="1">最多播放</span>
          <span data="2">最受欢迎</span>
          <span data="3">最新上传</span>
        </div>
        <div class="fr f12">已为您找到<b>0</b>个视频</div>
      </div>
                    <div class="bobo_list clearfix">
                    <ul>


                    </ul>
                    </div>
                    <div class="s_page">

                    </div>

                            </div>
      </div>
@endsection

@section('javascript')

<script language="JavaScript" src="{{ static_res('/vrhelp/js/plugin/jquery-slideShow.js') }}"></script>
<script>



  (function(){
    var videoList = {
      init:function(){
        //banner轮播
        var image=[@foreach($recommend['tdbobo-banner']['data'] as $item)
        "{!!static_image($item['image']['cover'])!!}",
        @endforeach];
        var href=[@foreach($recommend['tdbobo-banner']['data'] as $item)
        @if(!empty($item['link']))
        "{!!$item['link']!!}",
        @else
        "#",
        @endif
        @endforeach];

        $("#bobo_slide_list").slideShow({images:image,//必选
        autoPlay:true,
        href:href,
        height:240,//可指定轮播图高度
        interval:6000});
        //获取参数
        type = getUrlHash('type');
        sort = getUrlHash('sort');
        page = getUrlHash('page');
        if(type==''){
          type=30101;
        }
        if(sort==''){
          sort=1;
        }
        //添加active
        $('.clf1 ul li').each(function(k,v){
            if($(v).attr('data')==type){
              $(v).addClass('active');
            }
        })
        $('.v_option div span').each(function(k,v){
            if($(v).attr('data')==sort){
              $(v).addClass('active');
            }
        })
        //绑定事件
        var p = this;
        $('.v_option .fl span').click(function(){
            $('.v_option .fl span').removeClass('active');
            $(this).addClass('active');
            location.href = '#type='+type+'&sort='+$(this).attr('data')+'&page='+page;
            // p.parametChange();
        })

        $('.clf1 li a').click(function(){
            $('.clf1 li').removeClass('active');
            $(this).parent().addClass('active');
            location.href = '#type='+$(this).parent().attr('data')+'&sort=1&page=1'
            // p.parametChange();
        })

        $(window).bind('hashchange',function(){
          p.parametChange();
        })
        //获取数据
        this.parametChange();
      },
      parametChange:function(){
        type = getUrlHash('type');
        sort = getUrlHash('sort');
        page = getUrlHash('page');
        var p = this;
        $.post('/vrhelp/videoListInterface',{type:type,sort:sort,page:page},function(data){
          p.updateList(data.data);
        })
      },
      updateList:function(data){
        type = getUrlHash('type');
        sort = getUrlHash('sort');
        page = getUrlHash('page');
        if(data.total==null){
          data.total=0;
        }
        $('.v_option .fr b').text(data.total);

        imgurl = 'http://image.vronline.com/';
        html = '';
        $.each(data.videoList,function(k,v){
          html += '<li><a href="javascript:;" data-val="'+v.id+'" class="pr f12 video_play">'+
                    '<div class="video_sst" style="background-image:url('+imgurl+v.image.cover+');"></div>'+
                    '<span class="cover"></span>'+
                    '<span class="label pa f12 tac">3D</span>'+
                    '<span class="video_play_icon pa"><i class="video_triangle pa"></i></span>'+
                                            '<div class=" video_msg_con pa">'+
                                                '<h4 class="fl els f14">'+v.name+'</h4>'+
                                                   '<p class="video_play_num fr els">'+
                                                    '<span class="els fr">'+v.play+'</span><span class="min_play_icon fr pr">'+
                                                        '<i class="triangle pa"></i>'+
                                                    '</span>'+
                                                '</p>'+
                                                '<p class="fl f12 v_subtitle els">'+v.desc+'</p>'+
                                            '</div>'+
                                        '</a></li>';
        })
        $('.bobo_list ul').html(html);

        //分页
        count = 20;
        p = 1;
        n = Math.ceil(data.total / count);
        flag=0;
        if (page > 1) {
            p = page - 1;
        }
        if (page < Math.ceil(data.total / count)) {
            n = parseInt(page) + 1;
        }
        if(page<4){
          npage=3;
        }
        else{
          npage=page;
        }

        pagehtml = '<a href="/vrhelp/video/list#type='+type+'&sort='+sort+'&page='+p+'">上一页</a>';
        for(i=1;i<=Math.ceil(data.total / count);i++){
          if(i==1||i==2||i>npage-3&&i<parseInt(npage)+3){
            if(i==page){
              pagehtml += '<a href="/vrhelp/video/list#type='+type+'&sort='+sort+'&page='+i+'" class="active" >'+i+'</a>';
            }
            else{
              pagehtml += '<a href="/vrhelp/video/list#type='+type+'&sort='+sort+'&page='+i+'" >'+i+'</a>';
            }
          }
          else{
            if(i>2&&npage-i>2&&flag==0){
              pagehtml+='<a class="ellipsis">...</a>';
              flag=1;
            }
          }
        }
        if(Math.ceil(data.total / count)-npage>2){
                pagehtml+='<a class="ellipsis">...</a>';
        }
        pagehtml += '<a href="/vrhelp/video/list#type='+type+'&sort='+sort+'&page='+n+'">下一页</a>';
        $('.s_page').html(pagehtml);
      }
    }
    return videoList.init();
  })()

 
</script>
@endsection
