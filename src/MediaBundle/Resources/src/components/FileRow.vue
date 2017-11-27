<template>
<transition name="fade">
  <tr>
    <td>
      {{name}}
    </td>
    <td>
      {{humanType}}
    </td>
    <td>
      <component :is="previewComponent" :filename="filename" />
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
import Image from './types/Image';
import Other from './types/Other';

export default {
  props: ['name', 'id', 'type', 'humanType', 'filename'],
  data() {
    return {
      removing: false,
    }
  },
  computed: {
    previewComponent() {
      if (this.type === 'image') {
        return Image;
      }
      return Other;
    }
  },
  methods: {
    remove() {
      this.removing = true;
      const row = this;
      this.$store.dispatch('delete', this.id).catch(function(error) {
        Perform.base.showError(error.response.data.message);
        row.removing = false;
      });
    }
  }
}
</script>
