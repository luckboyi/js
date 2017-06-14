<template lang="html">
  <div class="account pr" id="account">
    <div class="account_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>确认订单</h3>
    </div>
    <div class="address_con pr" @click="modify_address()">
      <i class="icon pa location_icon"></i>
      <p class="address_msg els" title="">{{data.address}}</p>
      <p class="address_user"><b>{{data.username}}</b>{{data.phonenume}}</p>
      <router-link tag="i" to='/address' class="icon next_icon pa"></router-link>
    </div>
    <div class="account_con">
      <div class="account_goods_list">
        <ul>
          <router-link tag="li" :to="'/detail/'+item.id" v-for="item in data.goodslist" class="clearfix">
            <div class="img_con fl" :style="{backgroundImage:'url(' + item.img + ')'}"></div>
            <div class="goods_msg fl">
              <p class="els goods_name">{{item.goodsname}}</p>
              <p class="els special_price">{{item.discount_msg}}<b class="redColor">{{item.discount_state}}</b></p>
              <p class="els goods_buynum">数量：{{item.goods_num}}</p>
              <p class="els goods_price"><b class="redColor">￥{{item.price}}</b><i>(运费：￥{{item.freight}})</i><b class="fr">x{{item.num}}</b></p>
            </div>
          </router-link>
        </ul>
      </div>
      <div class="account_set">
        <p class="clearfix">合计：<b class="fr redColor">￥{{totalPrice}}</b></p>
        <p class="clearfix">运费：<b class="fr">￥{{freight_sub}}</b></p>
        <p class="clearfix"><b class="fr">共{{data.goodslist.length}}件商品</b></p>
      </div>
      <div class="pay_way">
        <p class="clearfix">支付方式 <b class="fr greyColor">更多支付方式即将推出</b></p>
        <div class="in_pay_way">
          <p v-for='(item,$index) in data.payway.in_payway' class="pr wx_pay"><i class="icon pa" v-if="item.classobj.length >0" :class="[item.classobj]"></i>{{item.payname}}<i class="pa icon select_icon" @click="selectFn($index)" :class="{cur:$index==data.payway.isselect}"></i></p>
        </div>
      </div>
    </div>
    <div class="account_total pa">
      合计：<i class="redColor">￥{{totalPrice + freight_sub}}</i><b class="greyColor">(含运费：￥{{freight_sub}})</b>
      <div  class="order_btn fr tac" @click="submitBtn()">
        <div class="in_order_btn tac">结算</div>
      </div>
    </div>
    <tipsComponent :tips= "tips"></tipsComponent>
  </div>
</template>

<script>
export default {
  name:'account',
  data(){
    return{
      data:{
        goodslist:[],
        payway:{}
      },
      tips:{
        isshow:false,
        msg:'',
        classObj:'suc_icon',
        callback:''
      }
    }
  },
  created(){
    this.getDate('./../../../data/accountdata.txt','',this.getAccount);
  },
  computed:{
    totalPrice:function(){
      var total = 0;
      if(this.data.goodslist.length>0){
        this.data.goodslist.forEach(function(good){
          total += good.price * good.num;
        });
      }

      return total;
    },
    freight_sub:function(){
      var freight = 0;
      if(this.data.goodslist.length>0){
        this.data.goodslist.forEach(function(good){
          freight += good.freight;
        });
      }
      return freight;
    }
  },
  methods:{
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          if(typeof callback === 'function'){
            //console.log(1)
            callback(JSON.parse(response.body).data);
          }
        }, response => {
          // error callback
        });
      })
    },
    getAccount(data){
      this.data= data;
    },
    goBack(){
      window.history.go(-1)
    },
    selectFn(i){
      //alert(1)
      var that = this;
      that.data.payway.isselect = i;
    },
    submitBtn(){
      var that = this;
          that.tips.isshow = true;
          that.tips.msg = '支付成功';
          that.tips.classObj = 'suc_icon';
          that.tips.callback = ''
    },
    modify_address(){

    }
  }
}
</script>

<style lang="scss"  scoped>
@import "../../style/base.scss";
.account{
  width: 100%;
  height: 100%;
  overflow: hidden;
  background: $greyBg;
  .account_con{
      height: 100%;
      overflow-x: auto;
      padding-bottom: 3.2rem;
      @include box-sizing(border-box);
  }
}
.account_head,.account_goods_list,.account_set,.pay_way{background: #fff;}
.location_icon{
  width: 11px;
  height: 15px;
  background-position:-28px -48px;
  left: 0.4rem;
}
.next_icon{
  width: 13px;
  height: 23px;
  background-position: -190px -25px;
  right: 0.4rem;
  @include topcenter();
}
.address_con{
  height: 0.88rem;
  color: #fff;
  background: $mainColor;
  padding: 0.12rem 0.6rem;
  @include box-sizing(border-box);
  p{
    line-height: 0.36rem;
    width: 100%;
    font-size: 0.26rem;

  }
  .address_user{
    b{
      margin-right: 0.14rem;
    }
  }
}
.account_goods_list{
  border-bottom: 1px solid $greyCol;
  padding: 0 0.15rem;
  @include box-sizing(border-box);
  li:nth-last-child(1){
    border:0;
  }
  li{
    border-bottom: 1px solid $greyCol;
    padding: 0.1rem 0;

    .img_con{
      width: 1.9rem;
      height: 1.9rem;
      background-position: center;
      background-size: cover;
    }
    .goods_msg {
      width: 70%;
      margin-left: 0.1rem;
      font-size: 0.3rem;
      padding: 0.1rem 0 0 0;
      .goods_name{
        line-height: 0.32rem;
        display: inline-block;
        font-weight: 400;
      }
      .special_price{
        font-size: 0.24rem;
        color: $greyCol;
        line-height: 0.36rem;
      }
      .goods_buynum{
        line-height:0.32rem;
        font-weight: 400;
      }
      .goods_price{
        font-size: 0.36rem;
        margin-top: 0.42rem;
        i{
          font-size: 0.24rem;
          color: $greyCol;
          margin-left: 0.16rem;
        }
      }
    }
  }

}
.account_set,.pay_way{
  padding: 0 0.15rem;
  margin: 0.16rem 0;
  border-top: 1px solid $greyCol;
  border-bottom: 1px solid $greyCol;
  @include box-sizing(border-box);
  p{
    display: inline-block;
    height: 0.86rem;
    width: 100%;
    border-bottom: 1px solid $greyCol;
    font-size: 0.28rem;
    font-weight: 600;
    padding: 0.26rem 0.2rem;
    @include box-sizing(border-box);
    &:nth-last-child(1){
      border: none;
    }
  }
}
.pay_way{
  margin: 0;
  .in_pay_way{
    p{
      text-indent: 0.8rem;
    }
    .wx_icon{
      width: 29px;
      height: 25px;
      background-position:-42px -48px;
      left: 0.2rem;
      @include topcenter();
    }
    .select_icon{
      right: 0.2rem;
      @include topcenter();
    }
  }
}
.account_total{
    height: 1.2rem;
    background: #fff;
    line-height: 1rem;
    width: 100%;
    bottom: 0;
    left: 0;
    font-size: 0.32rem;
    font-weight: 600;
    border-top: 1px solid $greyCol;
    padding: 0.1rem 0.1rem 0 0.26rem;
    @include box-sizing(border-box);
    b{
      font-size: 0.2rem;
      font-weight: normal;
      margin-left: 0.1rem;
    }
    .in_order_btn{
      font-size: 0.2rem;
    }
}
</style>
