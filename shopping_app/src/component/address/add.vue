<template lang="html">
  <div class="add_address" id="add_address">
    <div class="address_head tac">
      <h3 class="pr"><i class="icon back_icon pa"  @click="goBack()"></i>收货地址</h3>
    </div>
    <div class="address_con">
      <ul v-if="addressdata.length>0">
        <li class="pr" v-for="(item,index) in addressdata">
          <i class="icon select_icon pa" :class="{cur:item.isselect}" @click="selectFn(index)"></i>
          <p class="els">
            {{item.username}}<b>{{item.phonenume}}</b>
          </p>
          <p  class="els">{{item.address}}</p>
          <router-link class="icon editor_icon pa" to="/editor_address"  @click="editorFn(index)"></router-link>
        </li>
      </ul>
    </div>
    <router-link tag="div" to="/editor_address" class="add_new tac">
      <div class="">
        新增收货地址
      </div>
    </router-link>
  </div>
</template>

<script>
export default {
  data(){
    return{
      addressdata:[{
      }]
    }
  },
  mounted(){
    var that = this;
    that.$nextTick(function(){
      that.$http.get('../../../data/addressdata.txt').then(response => {
        //console.log(JSON.parse(response.body).data)
        that.addressdata =JSON.parse(response.body).data;
      }, response => {
        // error callback
      });
    })
  },
  methods:{
    goBack(){
      window.history.go(-1)
    },
    selectFn(i){
      var that = this;
      this.addressdata[i].isselect = !this.addressdata[i].isselect;
      if(that.addressdata[i].isselect == true){
        for(var k= 0 ;k < that.addressdata.length ;k++ ){
          console.dir(k)
          if(i != k){
            that.addressdata[k].isselect = false;
          }
        }
      }
    },
    editorFn(i){
      //alert(i)

    }
  },
}
</script>

<style lang="scss">
@import "../../style/base.scss";
.add_address{
  width: 100%;
  height: 100%;
  overflow: hidden;
}
.address_con{

  li{
    width: 100%;
    height: 1rem;
    border-bottom: 1px solid $greyCol;
    padding: 0 0.36rem;
    text-indent: 0.6rem;
    font-size: 0.3rem;
    @include box-sizing(border-box);
    font-weight: 600;
    p{
      padding: 0 0.6rem;
      text-align: left;
      text-indent: 0;
      margin-top: 0.1rem;
      b{
        font-weight: 600;
        margin-left: 0.16rem;
      }
    }
  }
  .select_icon {
    @include topcenter();
  }
  .editor_icon {
    @include topcenter();
    right: 0.36rem;
  }
}
.add_new{
  width: 100%;
  height: 0.8rem;
  padding: 0 0.8rem;
  margin-top: 0.2rem;
  div{
    width: 100%;
    height: 100%;
    background: $mainColor;
    border-radius: 4px;
    font-size: 0.3rem;
    line-height: 0.8rem;
    color: #fff;
  }
}
</style>
