<template>
  <div class="good_information" id="good_imformation">
    <div class="">
      <div class="goodsinfor_con" v-if="goodsinfor.iscur == true">
           <ul>
             <li v-for="item in goodsinfor.detail"  class="in_goodsinfor_con clearfix">
               <span class="els fl">{{item.name}}</span>
               <span class="els fl">{{item.data}}</span>
             </li>
           </ul>
      </div>
      <div class="goodsdescribe" v-if="goodsinfor.iscur != true">
          <p>{{goodsinfor.title}}</p>
          <p>{{goodsinfor.describe}}</p>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  name:'good_imformation',
  props:["goodsinfor"]
}
</script>

<style lang="scss">
.goodsinfor_con{
  .in_goodsinfor_con{
    padding-left: 0.2rem;
    width: 100% !important;
      span{
          display: inline-block;
          margin-right: 10px;
          &:nth-child(1){
            width: 20%;
          }
          &:nth-child(2){
            width: 70%;
          }
      }
  }

}
.goodsdescribe{
  color: #9e9e9e;
  p:nth-child(1){
    font-size: 0.36rem;
    line-height: 0.8rem;
  }
  p:nth-child(2){line-height: 0.36rem;}
}
</style>
