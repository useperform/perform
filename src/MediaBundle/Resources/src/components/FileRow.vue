<template>
  <tr>
    <td>
      {{name}}
    </td>
    <td>
    </td>
    <td>
    </td>
    <td>
      <a href="#" class="btn btn-danger" @click.prevent="deleteFile(id)">Delete</a>
    </td>
  </tr>
</template>

<script>
export default {
  props: ['name', 'id'],
  methods: {
    deleteFile(id) {
      this.$store.dispatch('delete', id);
    }
  }
}
</script>
