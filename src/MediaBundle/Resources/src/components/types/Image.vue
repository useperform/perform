<template>
  <img :src="url" />
</template>

<script>
export default {
  props: ['file'],
  computed: {
    url() {
      return '/uploads/thumbs/'+this.file.filename;
    }
  }
}
</script>
