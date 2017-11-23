import Vue from 'vue'
import VueRouter from 'vue-router'
import App from './components/App'
import Listing from './components/Listing'
import Upload from './components/Upload'
import store from './store/store'

Vue.use(VueRouter);

const router = new VueRouter({
  routes: [
    {path: '/', component: Listing},
    {path: '/upload', component: Upload},
  ]
});

const app = new Vue({
  el: '#perform-media-app',
  render: h => h(App),
  store,
  router
});
