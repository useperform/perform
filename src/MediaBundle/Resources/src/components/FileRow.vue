<template>
<transition name="fade">
  <tr>
    <td>
      {{name}}
    </td>
    <td>
    </td>
    <td>
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
export default {
  props: ['name', 'id'],
  data() {
    return {
      removing: false,
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
