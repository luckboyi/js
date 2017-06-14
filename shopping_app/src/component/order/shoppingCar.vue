<template lang="html">
  <div class="shoppingCar" id="shoppingCar">
    <div class="shoppingCar_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>购物车</h3>
    </div>
    <div class="shoppingList pr">
      <v-scroll :on-refresh="onRefresh" >
        <div class="inShoppingList">
          <div class="shoppingList_con pr">
            <ul v-if="cartdata.length >0">
              <v-touch tag="li" v-for="(list,$index) in cartdata" v-if="list.listdata.length>0" :class="{leftswipe:list.isleftswipe}" class="order_list pr"  v-on:swipeleft="onSwipeLeft($index)" ref="{{list.id}}">
                <div class="order_list_head clearfix">
                  <i class="select_icon icon  fl" :class="{cur:list.isselect}" @click="selectAll($index)"></i>
                  <div class="img_con fl">
                    <img :src="list.icon">
                  </div>
                  <div class="fl title_con">{{list.username}}</div>
                </div>
                <div class="order_list_con clearfix">
                  <div class="clearfix in_order_list_con clearfix" v-for="(item,index) in list.listdata">
                    <i class="select_icon icon  fl" :class="{cur:item.isselect}"  @click="selectFn(item,$index)"></i>
                    <router-link :to="'/detail/'+item.id" class="clearfix fl">
                      <div class="img_con fl">
                        <img :src="item.img" alt="">
                      </div>
                    </router-link>
                    <div class="goods_msg fl">
                      <p class="in_goods_msg ells2">{{item.info}}</p>
                      <p class="discount_msg">{{item.discount_msg}}<b>{{item.discount_state}}</b></p>
                      <p class="goods_num">{{item.goods_num}}</p>
                      <p class="clearfix price_con">
                        <span class="fl">￥{{item.price}}</span>
                        <span class="algorithm_con pr tac fr">
                          <i class="sub fl" @click.stop.prevent="item.num >1?item.num-=1:deleteFn($index,index)">-</i>
                          <b>{{item.num}}</b>
                          <i class="add fr" @click.stop.prevent="item.num+=1">+</i>
                        </span>
                      </p>
                    </div>
                  </div>
                </div>
                <div class="delete pa " @click.stop="deleteFn($index)">
                  <b class="pa">删除</b>
                </div>
                <div class="freight">
                  <div class="no_freight" v-show="list.freight <=0">免运费</div>
                  <div class="in_freight" v-show="list.freight >0">
                    运费:<b class="fr">￥：{{list.freight}}</b>
                  </div>
                </div>
              </v-touch>
            </ul>
            <div  v-if="cartdata.length <= 0"  class="shoppingCar_empty icon pa">
            </div>
          </div>
        </div>
      </v-scroll>
      <div class="total_sub pa">
        <div class="fl total_sub_left clearfix">
          <h4 class="fl">合计:<b>￥{{totalPrice + freight_sub}}</b></h4>
          <div class="fr">（含运费<b>￥{{freight_sub}}</b>）</div>
        </div>
        <div v-if="address !=''"  tag='div' to="/account"  class="order_btn fr tac" @click.stop.prevent="submitBtn()">
          <div class="in_order_btn tac">结算</div>
        </div>
        <router-link  v-if="address ==''" tag='div' to="/address"  class="order_btn fr tac" @click.stop.prevent="submitBtn()">
          <div class="in_order_btn tac">结算</div>
        </router-link>
      </div>
    </div>
    <tipsComponent :tips= "tips"></tipsComponent>
  </div>
</template>

<script>
import Scroll from './../scroll/scrooll.vue';
export default {
  name:'shoppingCar',
  components : {
    'v-scroll': Scroll
  },
  data(){
    return{
      cartdata:[],
      address:'',
      isDelete:false,
      userindex:'',
      goodsindex:'',
      disabled:true,
      tips:{
        isshow:false,
        msg:'',
        classObj:'suc_icon',
        callback:''
      }
    }
  },
  mounted(){
    this.getCartdata('./../../../data/cartdata.txt','')
    var that = this;
  },
  computed:{
    totalPrice:function(){
      var total = 0;
      if(this.cartdata.length >0){
        this.cartdata.forEach(function(good){
          if(good.isselect){
            //console.dir(1)
            good.listdata.forEach(function(item){
              if(item.isselect){
                total += item.price * item.num;
              }
            })
          }
        });
      }
      return total;
    },
    freight_sub:function(){
      var freight = 0;
      if(this.cartdata.length >0){
        this.cartdata.forEach(function(good){
          if(good.isselect){
            freight += good.freight;
          }
        });
      }
      return freight;
    }

  },
  methods:{
    submitBtn(){
      var that = this;
      this.cartdata.forEach(function(item){
        if(item.isselect){
          that.$router.push({path:'/account'})
        }else{
          that.tips.isshow = true;
          that.tips.msg = '您还没有选中想要购买的商品';
          that.tips.classObj = 'no_good';
          that.tips.callback = ''
        }
      })
    },
    deleteFn(i,k){
      var that = this;
      that.tips.isshow = true;
      that.tips.msg = '确定删除此商品吗？';
      that.tips.classObj = 'bad_icon';
      that.tips.callback =function(){
        if(k != undefined){
          that.cartdata[i].listdata.splice(k,1);
          if(that.cartdata[i].listdata.length == 0){
            that.cartdata.splice(i,1);
          }
        }else{
          that.cartdata.splice(i,1);
        }
      };
    },
    deleteGood(i,k){
      if(k != undefined){
        this.cartdata[i].listdata.splice(k,1);
      }else{
        this.cartdata.splice(i,1);
      }
    },
    cancelFn(){
      this.isDelete=false
    },
    sureFn(i,k){
      this.isDelete =false;
      if(k != undefined){
        this.cartdata[i].listdata.splice(k,1);
      }else{
        this.cartdata.splice(i,1);
      }
    },
    getCartdata(url,params){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          that.cartdata = JSON.parse(response.body).cartdata;
          that.totalPrice = JSON.parse(response.body).totalPrice;
          that.freight_sub = JSON.parse(response.body).freight_sub;
          that.address = JSON.parse(response.body).address;

        }, response => {
          // error callback
        });
      })
    },
    onSwipeLeft(i){
      console.log(i)
      this.cartdata[i].isleftswipe =!this.cartdata[i].isleftswipe;
      var that = this;
      $(window).on('click',function(){
        if(that.cartdata.length != 0){
          that.cartdata.forEach(function(item){
            item.isleftswipe = false;
          })
        }
      })
    },
    onRefresh(done) {
      this.getCartdata('./../../../data/cartdata.txt','')
      done() // call done
    },
    goBack(){
        window.history.go(-1)
    },
    selectFn(goodObj,i){
      var that = this;
      if(goodObj.isselect == void 0){
        that.$set(goodObj,"isselect",true)
      }else{
        goodObj.isselect = !goodObj.isselect;
      }
      that.cartdata[i].listdata.forEach(function(goods){
        if(goods.isselect){
          that.cartdata[i].isselect = true;
        }else if(!goods.isselect){
          that.cartdata[i].isselect = false;
        }
      })

    },
    selectAll(i){
      var that = this;
      that.cartdata[i].isselect = !that.cartdata[i].isselect;
      if(that.cartdata[i].isselect == true){
        console.log(11)
        console.log()
        that.cartdata[i].listdata.forEach(function(goods){
          goods.isselect =true;
        })
      }else{
        that.cartdata[i].listdata.forEach(function(goods){
          goods.isselect =false;
        })
      }
    }
  }

}
</script>
<style lang="scss">
@import "../../style/base.scss";
.shoppingCar{
  width: 100%;height: 100%;
}
.leftswipe{
  animation: swipeleft 1s forwards;
  -webkit-animation: swipeleft 1s forwards;
}
@keyframes swipeleft{
    form{
      left: 0;
    }
    to{
      left: -0.98rem;
    }
}
@-webkit-keyframes swipeleft{
    form{
      left: 0;
    }
    to{
      left: -0.98rem;
    }
}
.shoppingCar_head,.account_head,.address_head{
  font-size: 0.28rem;
  h3{
    line-height: 0.88rem;
    height: 0.88rem;
    font-weight: bold;
    border-bottom: 1px solid $borderCol;
  }
  .back_icon{
    margin-left: 0.15rem;
  }
}
.shoppingList{
  height: 100%;
  width: 100%;
  background: red;
  .yo-scroll .inner{top:-1.2rem;}
  .load-more{display: none;}
  .inShoppingList{
    @include box-sizing(border-box);
    height: 100%;
    .shoppingList_con{
      height: 100%;
      min-height: 6rem;
      background: lightblue;
      overflow-x: hidden;
      background: $greyBg;
    }
  }
}
.order_list{
  margin-top: 0.1rem;
  background: #fff;
  .delete{
    height: 100%;
    width: 0.98rem;
    z-index: 999;
    top:0;
    color: #fff;
    line-height:5.6rem;
    right: -0.98rem;
    text-align: center;
    font-size: 0.3rem;
    background: $mainColor;
    b{
      @include center();
      width: 100%;
    }
  }
  .order_list_head{
    padding: 0.06rem 0.26rem;
    @include box-sizing(border-box);
    line-height: 0.66rem;
    border-bottom: 1px solid $greyCol;
    .select_icon {margin-top: 8px;}
    .img_con{
      width: 0.66rem;
      height: 0.66rem;
      margin: 0 0.24rem;
      @include raduis(66rem);
      overflow: hidden;
    }
    .title_con{
      color: $titleCol;
    }
  }
  .order_list_con{
    border-bottom: 1px solid $greyCol;
    padding: 0.2rem 0.26rem;
    background: #fff;
    @include box-sizing(border-box);
    .in_order_list_con{
      margin-top: 0.2rem;
      &:nth-child(1){
        margin-top: 0;
      }
    }
    .select_icon{
      margin-top: 0.6rem;
    }
    .img_con{
      width: 1.92rem;
      height:1.5rem;
      margin:0 0.24rem;
      img{
        width: 100%;
        height: 100%;
      }
    }
    .goods_msg{
      width: 58%;
      font-size: 0.2rem;
      color:#333 ;
      .discount_msg{
        font-size: 0.14rem;
        color: $greyCol;
        margin:0.01rem 0 ;
          b{
            color: $specialRedCol;
          }
      }
      .price_con{
        font-size: 0.28rem;
        margin-top: 0.08rem;
        span{
          font-size: 0.3rem;
          color: $mainColor;
          &.algorithm_con{
            font-size: 0.2rem;
            color: #333;
            width: 1.10rem;
            line-height: 0.24rem;
            border: 1px solid $greyCol;
            border-radius: 2rem;
            .sub,.add{
              width: 0.24rem;
              height: 0.24rem;
            }
          }
        }
      }
    }
  }
  .freight{
    height: 0.8rem;
    line-height: 0.8rem;
    .no_freight{
      line-height: 0.8rem;
      text-align: right;
      width: 100%;
      padding: 0 0.26rem;
      font-weight: bold;
      @include box-sizing(border-box);
    }
    .in_freight{
      padding: 0 0.26rem;
      @include box-sizing(border-box);
    }
  }
}
.total_sub{
  height: 2rem;
  background: #fff;
  width: 100%;
  bottom: 0;
  left: 0;
  padding:0 0.1rem 0 0.26rem;
  @include box-sizing(border-box);
  .total_sub_left{
    height: 2rem;
    h4{
      height: 2rem;
      font-size: 0.3rem;
      font-weight: bold;
      line-height: 1.2rem;

    }
    b{
      color: $specialRedCol;
      font-size: 0.36rem;
    }
    div{
      display: inline-block;
      height: 2rem;
      font-size: 0.24rem;
      font-weight: bold;
      line-height: 1.2rem;
      color:$greyCol ;
      b{
        font-size: 0.3rem;
      }
    }
    .order_btn{
      margin:0;
    }
  }
}
.shoppingCar_empty{
  width: 200px;
  height: 216px;
  background-position:0  -90px;
  @include center();
}
</style>
