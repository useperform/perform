import Vue from 'vue';
import store from './store/store';
import router from './router';
import App from './components/App';
import preview from './preview';
import FormType from './components/FormType';

export default {
  // entry point to the main media application
  startApp(el)  {
    new Vue({
      el: el,
      render: h => h(App),
      store,
      router
    });
  },

  preview,

  form: {
    media(options) {
      new Vue({
        el: options.el,
        render: h => h(FormType, {props: {
          inputSelector: options.input,
          initialFile: options.file
        }}),
      });
    }
  },
};
