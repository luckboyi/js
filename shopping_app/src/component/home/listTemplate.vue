<template>
  <div id="goods_list" class="pr">
    <v-scroll :on-refresh="onRefresh" :on-infinite="onInfinite">
      <router-link tag="div" to="/special" class="banner_con">
        <img :src="active_goods.bg">
      </router-link>
      <div class="recommend_goods_list">
        <ul class="clearfix">
          <router-link tag="li" to="/special" :key="item.id" v-for="item in active_goods.goods_list" class="fl">
            <div class="img_con">
              <img :src="item.img">
            </div>
            <div class="price_con clearfix pr">
              <div class="original_price redColor fl">￥{{item.price}}</div>
              <div class="discounted_price fr line_through" v-if="item.discount_price>item.price">专柜价：{{item.discount_price}}</div>
              <div class=" special_price pa" >{{item.spe_price}}</div>
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
    </v-scroll>
  </div>

</template>
<script>
import Scroll from '../scroll/scrooll.vue';

  export default{
    components : {
      'v-scroll': Scroll
    },
    name:'',
    data(){
      return{
        active_goods:{

        },
        bannerList:[

        ],
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
      this.getDate('./../../../data/activedata.txt','',this.getActiveData);
      this.getDate('./../../../data/homebanner.txt','',this.getBannerData)
    },
    methods:{
      getDate(url,params,callback){
        var that = this;
        that.$nextTick(function(){
          that.$http.get(url,params).then(response => {
            // that.notice = response.body.data;
            if(typeof callback === 'function'){
              //console.log(1)
              console.dir(response.body.data)
              callback(JSON.parse(response.body).data);
            }
          }, response => {
            // error callback
          });
        })
      },
      getActiveData(data){
        this.active_goods = data;
      },
      getBannerData(data){
        console.log(data)
        this.bannerList = data;
      },
      onRefresh(done) {
        this.getDate('./../../../data/activedata.txt','',this.getActiveData);
        this.getDate('./../../../data/homebanner.txt','',this.getBannerData)
               done() // call done
      },
      onInfinite(done) {
          let vm = this;
          vm.$http.get('../../../data/listdata.txt').then((response) => {
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
$redColor:#ba4e48;
$borderCol:#e7e7e7;
$greyCol:#9e9e9e;
#goods_list{
  width: 100%;height: 100%;
  overflow-x: auto;
  padding: 0 0.15rem;
  .yo-scroll .inner{top: -1rem;}
  .load-more{display: none;}
  .banner_con{
    width: 100%;
    height: 5.38rem;
    @include border-radius(4px);
    overflow: hidden;
    img{width: 100%;height: 100%;}
  }
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
  .middle_banner_con{
    .banner_con{
      margin-bottom: 0.16rem;

    }
  }
}

</style>
