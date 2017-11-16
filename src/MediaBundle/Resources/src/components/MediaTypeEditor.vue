<template>
  <div>
    <b-btn v-b-modal.modal1>Select files</b-btn>

    <b-modal id="modal1" title="Select files" visible>
      <p>{{msg}}</p>
    </b-modal>
  </div>
</template>

<script>
import bButton from 'bootstrap-vue/es/components/button/button'
import bModal from 'bootstrap-vue/es/components/modal/modal'
import bModalDirective from 'bootstrap-vue/es/directives/modal/modal'

export default {
  data () {
    return {
      msg: 'Select files'
    }
  },
  components: {
    'b-btn': bButton,
    'b-modal': bModal
  },
  directives: {
    'b-modal' : bModalDirective
  },
}
</script>
