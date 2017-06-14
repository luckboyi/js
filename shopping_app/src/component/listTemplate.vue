<template>
  <div id="goods_list">
    <div class="banner_con">
      <img src="http://zhengyi.date/plante/src/image/gameimg1.png">
    </div>
    <div class="recommend_goods_list">
      <ul class="clearfix ">
        <router-link tag="li" to="/home"  :key="item.id" v-for="item in goods_list" class="fl">
          <div class="img_con">
            <img :src="item.img">
          </div>
          <div class="price_con clearfix pr">
            <div class="original_price redColor fl">￥{{item.price}}</div>
            <div class="discounted_price fr line_through" v-if="item.discount_price>item.price">专柜价：{{item.discount_price}}</div>
            <div class=" special_price pa" v-if="special_price !=''">{{item.special_price}}</div>
          </div>
          <div class="list_txt_con ells2">{{item.txt}}</div>
        </router-link>
      </ul>
    </div>
    <div class="middle_banner_con">
      <div class="banner_con" v-for="item in bannerList">
        <img :src="item.img">
      </div>
    </div>

  </div>

</template>
<script>
  export default{
    name:'',
    data(){
      return{
        goods_list:[
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
            price:189,
            discount_price:200,
            txt:'力开力朗力开力朗（LOCAL902户外多功能登山包',
            special_price:'',
            id:1111
          },
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
            special_price:'赠品',
            id:11112
          },
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
            special_price:'',
            id:11113
          }
        ],
        bannerList:[
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
          },
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
          },
          {
            img:'http://zhengyi.date/plante/src/image/gameimg1.png',
          }
        ]
      }
    }
  }
</script>
<style lang="scss">
@mixin border-radius($px){
  border-radius:$px;
  -webkit-border-radius:$px;

}
$redColor:#ba4e48;
$borderCol:#e7e7e7;
$greyCol:#9e9e9e;
.recommend_goods_list{
  margin: 0.16rem 0;
  width: 100%;
  height: 3.5rem;
  overflow: hidden;
  ul{
    display: inline-block;
    width:20rem;
    li{
      margin-right: 0.16rem;
      width: 1.96rem;
      height: 3.5rem;
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
#goods_list{
  width: 100%;height: 100%;
  overflow-x: auto;
  padding:0 0.15rem;
  .banner_con{
    width: 100%;
    height: 5.38rem;
    @include border-radius(4px);
    overflow: hidden;
    img{width: 100%;height: 100%;}
  }

  .middle_banner_con{
    .banner_con{
      margin-bottom: 0.16rem;

    }
  }
}

</style>
