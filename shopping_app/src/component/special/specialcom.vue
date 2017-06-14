<template lang="html">
  <div class="special_con" id="special_con">
      <div class="shoppingCar_head tac"><h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>推荐页面</h3></div>
      <div class="special_list pr">
        <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite">
          <listcomponent :listdata="listdata"></listcomponent>
        </v-scroll>
      </div>
    <foot></foot>
  </div>
</template>
<script>
import Scroll from './../scroll/scrooll.vue';
export default {
  name:'special',
  components : {
    'v-scroll': Scroll
  },
  data(){
    return{
      counter : 1, //默认已经显示出15条数据 count等于一是让从16条开始加载
      num : 6,  // 一次显示多少条
      pageStart : 0, // 开始页数
      pageEnd : 0, // 结束页数
      listdata: {
        pulldata:[],// 下拉更新数据存放数组
        downdata: []  // 上拉更多的数据存放数组
      }
    }
  },
  created(){
    this.getListDate('./../../../data/listdata.txt','');
  },
  methods:{
    goBack(){
      window.history.go(-1);
    },
    getDate(url,params,callback){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          console.log(typeof callback)
          if(typeof callback === 'function'){
            //console.log(1)
            callback(JSON.parse(response.body).data);

          }
        }, response => {
          // error callback
        });
      })
    },
    getListDate(url,params){
      var that = this;
      that.$nextTick(function(){
        that.$http.get(url,params).then(response => {
          // that.notice = response.body.data;
          this.listdata.pulldata = JSON.parse(response.body).data.slice(0,that.num);
        }, response => {
          // error callback
        });
      })
    },
    getListCon(data){
      this.listdata = data;
    },
    onRefresh(done) {
      this.getListDate('./../../../data/listdata.txt','');
             done() // call done
    },
    onInfinite(done) {
        let vm = this;
        vm.$http.get('./../../../data/listdata.txt').then((response) => {
            vm.counter++;
            vm.pageEnd = vm.num * vm.counter;
            vm.pageStart = vm.pageEnd - vm.num;
            let arr = JSON.parse(response.body).data;
            let i = vm.pageStart;
            let end = vm.pageEnd;
            console.dir(vm.counter)
            for(; i<end; i++){
              let obj ={};
              obj = arr[i];
              vm.listdata.downdata.push(obj);
               if((i + 1) >= JSON.parse(response.body).data.length){
                  this.$el.querySelector('.load-more').style.display = 'none';
                  return;
                }
            }
            done() // call done
             }, (response) => {
              console.log('error');
          });
     },
  }
}
</script>

<style lang="scss" >
@import "../../style/base.scss";
.special_con{
  width: 100%;
  height: 100%;
  overflow: hidden;
  .special_list{
    height: 100%;
    overflow-x: auto;
    padding:0 0 2rem 0;
    background: $greyBg;
    @include box-sizing(border-box);
    .yo-scroll .inner{
        top: -1.2rem;
    }
    .goods_list_con{
      padding-bottom: 2rem;
    }
  }
}
</style>
