import Vue from 'vue'
import MediaTypeEditor from './components/MediaTypeEditor'

Vue.config.productionTip = false

new Vue({
  el: '.perform-media-type',
  render: h => h(MediaTypeEditor)
})
