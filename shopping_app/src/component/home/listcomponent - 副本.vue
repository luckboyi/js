<template lang="html">
  <div class="goods_list_con">
    <div class="goods_list_container clearfix">
      <div class="fl in_goods_list_con in_goods_list_left">
        <ul class="">
          <router-link tag="li" :to="'/detail/'+item.id" :key="item.id"  v-for="item in listdata.goods_list_left_con">
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
          </router-link>
        </ul>
      </div>
      <div class="fr in_goods_list_con in_goods_list_right">
        <ul class="">
          <router-link tag="li" :to="'/detail/'+item.id" :key="item.id" v-for="item in listdata.goods_list_right_con" >
            <div class="img_con">
              <img :src="item.img">
            </div>
            <div class="list_txt_con ells2">{{item.txt}}</div>
            <p class="list_num">
              <span>数量：{{item.num}}</span>
              <span>{{item.kind}}</span>
            </p>
            <div class="price_con clearfix pr">
              <div class="original_price redColor fl">￥{{item.price}}</div>
              <div class="fr commend_con">
                <span class="pr" v-if="item.commend_num >0"><i class="pa icon good_commend_icon"></i>{{item.commend_num}}</span>
                <span class="pr"  v-if="item.commend > 0 "><i class="pa icon commend_icon"></i>{{item.commend}}</span>
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
  },
  methods:{
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          console.log(typeof callback)
          if(typeof callback === 'function'){

            callback(response.body.data);
          }
        }, response => {
          // error callback
        });
      })
    },
    getListCon(data){
      //this.listdata = data;
    }
  },
  created(){
    //this.getDate('http://localhost:8080/data/listdata.json','',this.getListCon);
  }
}
</script>

<style lang="scss" >
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
.goods_list_con{

  padding: 0 0.15rem;
  background: $greyBg;
  .goods_list_container{
    margin-left: -0.15rem;
    .in_goods_list_con{
      width: 50%;
      padding-left: 0.15rem;
      background: $greyBg;
      @include box-sizing(border-box);
      ul{
        li{
          background: #fff;
          width: 100%;
          height: 4.36rem;
          margin: 0.15rem 0;
          @include border-radius(4px);
          border: 1px solid $borderCol;
          overflow: hidden;
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
          &:nth-child(2n){
            height:5.2rem;
            .img_con{
              height:3.6rem;
            }
          }
        }

      }
      &.in_goods_list_right{
        ul{
            li{
              height: 5.2rem;
              .img_con{
                height:3.6rem;
              }
              &:nth-child(2n){
                height:4.36rem;
                .img_con{
                  height:2.8rem;
                }
              }
            }
        }

      }
    }
  }

}
</style>
