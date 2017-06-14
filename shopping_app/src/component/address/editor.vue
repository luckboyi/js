<template lang="html">
  <div class="editor_address pr" id="editor_address">
    <div class="account_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>填写订单</h3>
    </div>
    <div class="editor_con">
      <p>
        <span>亲爱的会员：</span>
        <span>您当前会员等级为<b class="redColor">VIP1</b>，可以填写1个收货地址</span>
      </p>
      <p>
        <input type="text" name=""  v-on:focus="maskmsg=''" placeholder="填写收货人真实姓名" v-model="userName" value="userName" v-on:blur="judge()">
      </p>
      <citySelect v-model="cityInfo" ></citySelect>
      <p>
        <input type="text" name="" v-on:focus="maskmsg=''"  placeholder="填写详细收货地址" v-model="address" value="">
      </p>
    </div>
    <div class="editor_btn pa tac" @click="saveFn()">
      保存收货信息
    </div>
    <div class="mask_con pa tac" v-if="maskmsg !=''">{{maskmsg}}</div>
  </div>

</template>

<script>
import citySelect from './citySelect.vue';
Vue.component('citySelect',require('./citySelect.vue'))
export default {
  component:{citySelect},
  data(){
    return{
      cityInfo:'',
      userName:'',
      address:'',
      maskmsg:''
    }
  },
  created: function() {
    let vm = this;
    vm.getSelectLists();  //实例已经创建完成之后获取城市下拉列表
  },
  watch: {
    resCity: 'getSecondSelectLists'  //监听城市值变化，获取城市对应辖区的下拉列表
  },
  methods: {
    maskHide(){
      console.log(1)
        this.maskmsg = '';
    },
    saveFn(){
      if(this.userName.length <2){
        this.maskmsg='收货人姓名不能少于2个字符';
        return false ;
      };
      if(this.address.length < 5){
        this.maskmsg='收货地址不能少于5个字符';
        return false ;
      }
    },
    judge(){

    },
    goBack(){
      window.history.go(-1);
    },
    getSelectLists: function() {
      let vm = this;
      console.log(vm.$route)
      if(vm.$route.name == 'modif') { //判断编辑页面获取编辑详情数据
      vm.getDetails(vm.$route.params.id);
      }
    },
    getSecondSelectLists: function(){},
    getDetails:function(data){
      setTimeout(function() {
        vm.resArea = data.id; //延时绑定辖区的下拉选项，为了辖区下拉数据先加载
      }, 300);
 }
 }
}
</script>

<style lang="scss">
@import "../../style/base.scss";
.editor_address{
  width: 100%;
  height: 100%;
  overflow: hidden;
  .mask_con{
    padding: 0.3rem 0.3rem;
    background: rgba(0,0,0,.8);
    position: absolute;
    z-index: 99;
    @include center();
    font-size: 0.3rem;
    color: #fff;
    border-radius: 4px;
    width: 60%;
  }
  .editor_btn{
    width: 100%;
    height: 0.8rem;
    line-height: 0.8rem;
    left: 0;
    bottom: 0;
    color: #fff;
    background: $mainColor;
    font-size: 0.3rem;
  }
}
.editor_con{
  p{

    span{
      &:nth-child(1){
        width: 100%;
        display: inline-block;
      }
      line-height: 0.2rem;
      padding: 0.2rem 0;
      color: #000;
    }
    display: inline-block;
    width: 100%;
    height: 1rem;
    padding: 0 0.2rem;
    @include box-sizing(border-box);
    border-bottom: 1px solid $greyBg;
    input{
      width: 100%;
      height: 100%;
      font-size: 0.3rem;
    }
    select{
      width: 100%;
      height: 100%;
      border: none;
      outline: none;
      font-size: 0.3rem;
      color: $greyCol;
      appearance:none;
      -moz-appearance:none;
      -webkit-appearance:none;
      background: url("../../image/next_icon.png") no-repeat scroll right center transparent;
    }
    select::-ms-expand { display: none; }
  }

}
</style>
