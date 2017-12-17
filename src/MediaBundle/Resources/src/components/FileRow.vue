<template>
<transition name="fade">
  <tr>
    <td>
      {{file.name}}
    </td>
    <td>
      {{file.humanType}}
    </td>
    <td>
      <FilePreview :file="file" />
    </td>
    <td>
      <button class="btn btn-danger" @click.prevent="remove" :disabled="removing">
        <i class="fa fa-spin fa-circle-o-notch" v-if="removing"></i>
        <span v-else>Delete</span>
      </button>
    </td>
  </tr>
</transition>
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
