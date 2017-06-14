<template lang="html">
  <div class="sec_nav pr" id="sec_list">
    <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite">
      <ul class="clearfix">
        <!-- <router-link tag="li" class="fl" v-for="item in navList" :class="">{{item.title}}</router-link> -->
        <li class="fl tac nav_list" v-for="item in navList" >
          <div class="in_list_con" :class="item.class">
            {{item.title}}
          </div>
        </li>
      </ul>
      <listcon :listcon="listcon"></listcon>
      <listcomponent :listdata="listdata"></listcomponent>
    </v-scroll>
  </div>
</template>

<script>
import Scroll from '../scroll/scrooll.vue';


export default {
  name:'list',
  components : {
    'v-scroll': Scroll
  },
  data(){
    return{
      navList:[],
      listcon:{},
      counter : 1, //默认已经显示出15条数据 count等于一是让从16条开始加载
      num : 4,  // 一次显示多少条
      pageStart : 0, // 开始页数
      pageEnd : 0, // 结束页数
      listdata: {
        pulldata:[],// 下拉更新数据存放数组
        downdata: []  // 上拉更多的数据存放数组
      }

    }
  },
  created(){
    this.getDate('./../../../data/secNavdata.txt','',this.getSecNav);
    this.getListDate('./../../../data/listdata.txt','');
  },
  methods:{
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
    getSecNav(data){
      this.navList = data.navList;
      this.listcon = data.active_goods;
    },
    getListCon(data){
      this.listdata = data;
    },
    onRefresh(done) {
      this.getDate('./../../../data/secNavdata.txt','',this.getSecNav);
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

<style lang="scss">
@mixin border-radius($px){
  border-radius:$px;
  -webkit-border-radius:$px;
}
.listbg1.in_list_con{background: #f66b64 !important;}
.listbg2.in_list_con{background: #fab384 !important;}
.listbg3.in_list_con{background: #8f29ee !important;}
.listbg4.in_list_con{background: #36d18b !important;}
.listbg5.in_list_con{background: #7be3e3 !important;}
.listbg6.in_list_con{background: #fcc214 !important;}
.listbg7.in_list_con{background: #f1605a !important;}
#sec_list .yo-scroll .inner{top: -1rem;}
#sec_list{
  width: 100%;
  height: 100%;
  overflow-x: auto;

    li.nav_list{
      width: 20%;
      color: #fff;
      font-size: 0.22rem;
      margin-bottom: 0.14rem;
      .in_list_con{
        width: 0.86rem;
        height: 0.86rem;
        line-height: 0.86rem;
        display: inline-block;
        @include border-radius (50%);
        background: #f66b64;
      }
    }
}
</style>
