<template>
<b-modal title="Select files" ref="modal" hide-footer>
  <p-file v-for="item in items" :item="item" @select="selectItem" />
</b-modal>
</template>

<script>
import bModal from 'bootstrap-vue/es/components/modal/modal'
import File from './File.vue'

export default {
  props: ['onSelect'],
  data () {
    return {
      items: [
        {name: 'hello.jpg'},
        {name: 'world.jpg'},
      ]
    }
  },
  components: {
    'b-modal': bModal,
    'p-file': File,
  },
  methods: {
    show() {
      this.$refs.modal.show();
    },
    selectItem(e) {
      if (this.onSelect) {
        this.onSelect(e);
      }
      this.$refs.modal.hide();
    }
  }
}
</script>
