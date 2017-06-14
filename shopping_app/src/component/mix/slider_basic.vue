<template>
  <div id="waterfall" >
    <ul class="clearfix waterfall_con pr" id="in_waterfall">
        <li v-for="item in items" class="fl waterfall_container pa">
            <div class="in_waterfall_con tac pr">
                <div class="img_con disin" >
                    <img v-bind:src="item.src">
                </div>
                <div class="title_con pa">
                    <p>{{item.title}}</p>
                </div>
            </div>
        </li>
    </ul>
  </div>
</template>

<script type="text/javascript">
export default{
    data () {
      return {
        items: [
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg2.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg3.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg4.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg5.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg6.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            },
            {
                src:'http://zhengyi.date/plante/src/image/gameimg1.png',
                title:'特价游戏'
            }
        ],
      }
    },
    methods:{
      init : function(){
        window.addEventListener('resize',function(){
            clearTimeout(time);
            time = setTimeout(function(){
             wf.arrange();
            }, 500);
        });
        setTimeout(function(){
            $items = document.querySelectorAll('#in_waterfall li');
            itemWidth= $items[0].offsetWidth;
            $items[0].style.height = Math.round(Math.random()*300) +itemWidth +'px';
            wf.arrange();
        }, 0)
      }
    },
    created:function(){
      this.init();
    }
}
//初始化
var $items,itemWidth,time;
var wf = {
    rdmHeight : function(){
        return Math.round(Math.random()*200) +itemWidth;
    },
    arrange:function(){
        var colsHeight = [],cols = 2 ;
        for(var i = 0 ; i< cols ; i++){
            colsHeight.push(0);
        }
        $items.forEach(function(ele,idx){
            var r = /^\+?[1-9][0-9]*$/;
            var curHeight = colsHeight[0],col = 0;
            for(var i = 0 ; i<colsHeight.length ; i ++ ){
                if(colsHeight[i] <curHeight){
                    curHeight = colsHeight[i];
                    col = i;
                }
            }
            ele.style.left = col * itemWidth + 'px';
            ele.style.height = wf.rdmHeight() +'px';
            ele.style.top = curHeight +'px';
            colsHeight[col] += ele.offsetHeight;
        })
    },
}



//

//获取滚动信息
var getScrollTop = function (element){
  if(element){
    return element.scrollTop
  } else {
    return document.documentElement.scrollTop;
  }
}
//随机高度，布局函数
</script>
<style lang="scss">

    .disin{display:inline-block;}
    $padding:0.1rem ;
    @mixin box-sizing(){
        box-sizing:border-box;
        -webkit-box-sizing:border-box;

    }
    @mixin center($translate){
        transform:$translate;
        -webkit-transform:$translate;
        -moz-transform:$translate;
    }
    @mixin border-radius($px){
        border-radius:$px;
        -webkit-border-radius:$px;
    }
    #waterfall{
        width:100%;
        .waterfall_con{
            width:100%;
            @include box-sizing;
            position: relative;
        }
        .waterfall_container{
            width:50%;
            overflow: hidden;
            @include box-sizing;
             padding:6px;
            .in_waterfall_con{
                width:100%;
                height:100%;
                background:#ddd;
                border-radius:5px;
                padding:$padding;
                @include box-sizing;
                .img_con {
                    width:100%;
                    height:100%;
                    padding-bottom:1.0rem;
                    img{
                        width:100%;
                        height:100%;
                    }
                }
                .title_con {
                    height:1.2rem;
                    width:100%;
                    bottom:-0.1rem;
                    padding:$padding;
                    left:50%;
                    border-radius:5px;
                overflow: hidden;

                    @include center(translateX(-50%));
                    p{
                        background:lightblue;
                        height:100%;
                        border-radius:5px;
                    }
                }
            }
        }
    }

</style>
