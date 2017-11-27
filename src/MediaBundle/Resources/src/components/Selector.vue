<template>
<bModal title="Select files" size="lg" ref="modal">
  <Listing v-bind:allowUpload="false" v-bind:layout="1" v-bind:lockLayout="true" />
  <div slot="modal-footer" class="w-100">
    <bBtn size="sm" variant="secondary" @click="hide">
      Cancel
    </bBtn>
    <bBtn size="sm" variant="primary" @click="select">
      Select
    </bBtn>
  </div>
</bModal>
</template>

<script>
import bModal from 'bootstrap-vue/es/components/modal/modal'
import bBtn from 'bootstrap-vue/es/components/button/button'
import File from './File'
import Listing from './Listing'

export default {
  props: ['onSelect'],
  components: {
    bModal,
    bBtn,
    File,
    Listing,
  },
  methods: {
    show() {
      this.$refs.modal.show();
    },
    hide() {
      this.$refs.modal.hide();
    },
    select() {
      if (this.onSelect) {
        this.onSelect(this.$store.getters.selectedFiles);
      }
      this.$refs.modal.hide();
    }
  }
}
</script>
