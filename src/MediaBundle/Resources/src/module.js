import Vue from 'vue';
import store from './store/store';
import router from './router';
import App from './components/App';

export default {
  // entry point to the main media application
  startApp(el)  {
    new Vue({
      el: el,
      render: h => h(App),
      store,
      router
    });
  }
};
