<template>
  <img :src="url" />
</template>

<script>
export default {
  props: ['filename'],
  computed: {
    url() {
      return '/uploads/thumbs/'+this.filename;
    }
  }
}
</script>
