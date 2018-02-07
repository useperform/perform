<template>
  <div class="p-comp-media-filerow row">
    <div class="col-1">
      <FilePreview :file="file" />
    </div>
    <div class="col-5">
      {{file.name}}
    </div>
    <div class="col-2">
      {{file.humanType}}
    </div>
    <div class="col-4 actions">
      <a :href="file.url" target="_blank">
        <i class="fa fa-lg fa-eye"></i>
      </a>
      <a :href="file.url" :download="file.name">
        <i class="fa fa-lg fa-download"></i>
      </a>
      <a href="#" @click.prevent="remove" :disabled="removing">
        <i class="fa fa-spin fa-circle-o-notch" v-if="removing"></i>
        <i class="fa fa-lg fa-trash" v-else></i>
      </a>
    </div>
  </div>
</template>

<script>
import FilePreview from './FilePreview';

export default {
  props: ['file'],
  data() {
    return {
      removing: false,
    }
  },
  components: {
    FilePreview,
  },
  methods: {
    remove() {
      this.removing = true;
      const row = this;
      this.$store.dispatch('delete', this.file.id).catch(function(error) {
        Perform.base.showError(error.response.data.message);
        row.removing = false;
      });
    }
  }
}
</script>
