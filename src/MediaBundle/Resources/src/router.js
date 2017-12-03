import Vue from 'vue';
import VueRouter from 'vue-router';
import Listing from './components/Listing';
import Upload from './components/Upload';

Vue.use(VueRouter);

export default new VueRouter({
  routes: [
    {path: '/', component: Listing},
    {path: '/upload', component: Upload},
  ]
});
