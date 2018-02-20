<template>
  <img :src="url" />
</template>

<script>
export default {
  props: ['file'],
  computed: {
    url() {
      return this.file.thumbnail;
    }
  }
}
</script>
