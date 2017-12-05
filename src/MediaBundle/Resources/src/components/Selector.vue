<template>
<bModal :title="title" size="lg" ref="modal" @hide="$refs.listing.reset()">
  <Listing
     v-bind:allowUpload="false"
     v-bind:initialLayout="1"
     v-bind:lockLayout="true"
     v-bind:allowSelect="true"
     :selectLimit="limit"
     ref="listing"
     />
  <div slot="modal-footer" class="w-100">
    <bBtn size="sm" variant="secondary" @click="cancel">
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
  props: ['onSelect', 'limit', 'multiple'],
  components: {
    bModal,
    bBtn,
    File,
    Listing,
  },
  computed: {
    title() {
      return this.multiple ? 'Select files' : 'Select file';
    }
  },
  methods: {
    show() {
      this.$refs.modal.show();
    },
    cancel() {
      this.$refs.modal.hide();
    },
    select() {
      if (this.onSelect) {
        this.onSelect(this.$refs.listing.selectedFiles);
      }
      this.$refs.modal.hide();
    }
  }
}
</script>
