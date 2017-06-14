import Vue from 'vue';
import App from './App.vue';
import VueRouter from 'vue-router';
import VueResource from 'vue-resource'
import   './js/base.js';
import jquery from './js/jquery-1.12.3.min.js';
import Scroll from './component/scroll/scrooll.vue';
import lrz from 'lrz'
window.$ = jquery;
// import AlloyFinger from 'alloyfinger/alloy_finger' // 手势库
// import AlloyFingerVue from 'alloyfinger/vue/alloy_finger.vue'
// import directives from './js/directives.js';
//
// Vue.use(AlloyFingerVue, {AlloyFinger});

//directives(Vue)
var Vuetouch = require('vue-touch');
Vue.use(Vuetouch,{name:'v-touch'})
//require  './style/base.css'
//import router from './js/router.js'
//开启debug的模式
Vue.config.debug = true ;
window.Vue = Vue;
Vue.use(VueRouter);
Vue.use(VueResource);
Vue.component('listcon',require('./component/home/listcon.vue'));
Vue.component('goodsinfor',require('./component/detail/goodsinfo.vue'));
Vue.component('order',require('./component/detail/order.vue'));
Vue.component('tipsComponent',require('./component/tips/tips.vue'));
Vue.component('special',require('./component/special/specialcom.vue'))
Vue.component('foot',require('./component/foot/foot.vue'));
Vue.component('listcomponent',require('./component/home/listcomponent.vue'));
const router = new VueRouter({
    base: __dirname,
    routes: [{
        path: '/',
        redirect: '/home/1'
    }, {
        path: '/home', //自定义子路由
        title:'home',
        component: require('./component/home/home.vue'),
        children:[
          {
            path:'/',
            redirect: '/home/1'
          },
          {
            path:'/home/1',
            component:require('./component/home/listTemplate.vue')
          },
          {
            path:'/home/:id',
            component:require('./component/home/list.vue')
          }
        ]
    }, {
        path: '/cart',
        require:'/shoppingCar'

    }, {
        path: '/order',
        component: require('./component/order/order.vue')

    },
    {
      path: '/special',
      component: require('./component/special/specialcom.vue')
    },
    {
        path: '/my',
        component: require('./component/my/personal.vue')
        //component: require('./component/scroll/myScroll.vue')
    },
    {
        path: '/personalmsg',
        component: require('./component/my/personalmsg.vue')

    },
    {
        path: '/member',
        component: require('./component/my/member.vue')

    },
    {
      path:'/account',
      component:require('./component/order/account.vue')
    },
    {
      path:'/address',
      component:require('./component/address/add.vue')
    },
    {
      path:'/editor_address',
      component:require('./component/address/editor.vue')
    },
    {
      path:'/shoppingCar',
      component:require('./component/order/shoppingCar.vue')
    },
    {
      path:'/detail/:id',
      component:require('./component/detail/detail.vue'),
      children:[
        {
          path:'/detail/:id/goodsinfor',
          component:require('./component/detail/goodsinfo.vue')
        },
        {
          path:'/detail/:id/goodsdescribe',
          component:require('./component/detail/goodsinfo.vue')
        }
      ]
    },

    ]
})

//创建一个app实例，并且挂载到app匹配的元素上
const app = new Vue({
    router: router,
    render: h => h(App)
}).$mount('#app')
