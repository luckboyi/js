<template lang="html">
  <div id="special_list_con">
    <router-link tag="div" to="/special" class="banner_img" :style="{background:'url('+listcon.bg+') center no-repeat '}">
    </router-link>
    <div class="recommend_goods_list">
      <ul class="clearfix">
        <router-link tag="li" to="/special" :key='item.id' v-for="item in listcon.goods_list" class="fl">
          <div class="img_con">
            <img :src="item.img">
          </div>
          <div class="price_con clearfix pr">
            <div class="original_price redColor fl">￥{{item.price}}</div>
            <div class="discounted_price fr line_through" v-if="item.discount_price>item.price">专柜价：{{item.discount_price}}</div>
            <div class=" special_price pa" >{{item.special_price}}</div>
          </div>
          <div class="list_txt_con ells2">{{item.txt}}</div>
        </router-link>
      </ul>
    </div>
  </div>
</template>
<script>
export default {
  props:["listcon"],
  data(){
    return{
    }
  },

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

#special_list_con{
  .banner_img{
    width: 100%;
    height: 4.3rem;
    padding: 0 0.15rem;
    background-size: cover !important;
    -webkit-background-size: cover;
    @include border-radius(6px);
  }
}
.recommend_goods_list{
  margin: 0.16rem 0;
  width: 100%;
  height: 3.5rem;
  padding: 0 0.15rem;
  overflow: hidden;
  ul{
    display: inline-block;
    width:20rem;
    li{
      margin-right: 0.16rem;
      width: 1.96rem;
      background: #fff;
      border: 1px solid $borderCol;
      overflow: hidden;
      .img_con{
        width: 100%;
        height: 2.36rem;
        img{
          width: 100%;
          height: 100%;
        }
      }
      .price_con{
          margin:0.1rem 0;
          font-size: 0.16rem;
          width: 1.96rem;
          .original_price{
            width: 0.6rem;
            font-size: 0.28rem;
            font-weight: 700;
          }
          .discounted_price{
            font-size: 0.12rem;
            transform: scale(0.8);
            width: 1.36rem;
            color: $greyCol;
          }
          .special_price{
            padding: 0.04rem;
            color:#fff;
            text-align: center;
            background:$redColor;
            right: 0.1rem;top:0;
          }
      }
      .list_txt_con{font-size: 0.1rem; line-height: 0.28rem;color: $greyCol;width: 100%;padding: 0 0.04rem;}
    }
  }

}

</style>
