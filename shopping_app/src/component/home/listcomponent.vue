<template lang="html">
  <div class="goods_list_con">
    <div class="goods_list_container clearfix">
      <div class="fl in_goods_list_con">
        <ul class="">
          <router-link tag="li" :to="'/detail/'+item.id" :key="item.id"  v-for="item in listdata.pulldata">
            <div class="in_con">
              <div class="img_con">
                <img :src="item.img">
              </div>
              <div class="list_txt_con ells2">{{item.txt}}</div>
              <p class="list_num">
                <span>数量：{{item.num}}</span>
                <span>{{item.kind}}</span>
              </p>
              <div class="price_con clearfix pr">
                <div class="original_price redColor fl ">￥{{item.price}}</div>
                <div class="fr commend_con">
                  <span class="pr" v-if="item.commend_num >0"><i class="pa icon good_commend_icon" :class="{good_commend_icon_sel:item.is_commend}"></i>{{item.commend_num}}</span>
                  <span class="pr"  v-if="item.commend > 0 "><i class="pa icon commend_icon"></i>{{item.commend}}</span>
                </div>
              </div>
            </div>
          </router-link>
          <router-link tag="li" :to="'/detail/'+item.id" :key="item.id"  v-for="item in listdata.downdata">
            <div class="in_con">
              <div class="img_con">
                <img :src="item.img">
              </div>
              <div class="list_txt_con ells2">{{item.txt}}</div>
              <p class="list_num">
                <span>数量：{{item.num}}</span>
                <span>{{item.kind}}</span>
              </p>
              <div class="price_con clearfix pr">
                <div class="original_price redColor fl ">￥{{item.price}}</div>
                <div class="fr commend_con">
                  <span class="pr" v-if="item.commend_num >0"><i class="pa icon good_commend_icon" :class="{good_commend_icon_sel:item.is_commend}"></i>{{item.commend_num}}</span>
                  <span class="pr"  v-if="item.commend > 0 "><i class="pa icon commend_icon"></i>{{item.commend}}</span>
                </div>
              </div>
            </div>
          </router-link>
        </ul>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  props:["listdata"],
  data(){
    return{

    }
  }
}
</script>

<style lang="scss">
$redColor:#ba4e48;
$borderCol:#e7e7e7;
$greyCol:#9e9e9e;
$greyBg:#f0efea;
@mixin border-radius($px){
  border-radius: $px;
  -webkit-border-radius: $px;
}
@mixin box-sizing($box){
  box-sizing: $box;
}
.yo-scroll{
  background:#f0efea ;
}
.goods_list_con{

  padding: 0 0.15rem;
  background: $greyBg;
  .goods_list_container{
    margin-left: -0.15rem;
    .in_goods_list_con{
      width: 100%;
      padding-left: 0.15rem;
      background: $greyBg;
      @include box-sizing(border-box);
      ul{
        -moz-column-count:2; /* Firefox */
       -webkit-column-count:2; /* Safari 和 Chrome */
       column-count:2;
       -moz-column-gap: 0.15rem;
       -webkit-column-gap: 0.15rem;
       column-gap: 0.15rem;
        li{
          width: 100%;
          height: 4.36rem;
          @include border-radius(4px);
          -moz-page-break-inside: avoid;
          -webkit-column-break-inside: avoid;
          break-inside: avoid;
          padding: 0.15rem 0 0 0;
          .in_con{
            background: #fff;
            border: 1px solid $borderCol;
            @include border-radius(4px);
            width: 100%;
            height: 100%;
            overflow: hidden;
          }
          .img_con{
            width: 100%;
            height: 2.8rem;
            img{
              width: 100%;
              height: 100%;
            }
          }
          .list_num{
            padding: 0 0.15rem;
            color: #9e9e9e;
          }
          .list_txt_con {
            font-size: 0.14rem;
            line-height: 0.26rem;
            margin: 0.1rem 0;
            padding: 0 0.14rem;
          }
          .price_con{
            padding: 0 0.15rem;
            margin-top: 0.15rem;
            .original_price {
              font-size: 0.3rem;
            }
          }
          .commend_con{
            span{
              padding-left:  22px;
            }
          }
        }

      }
    }
  }

}
</style>
