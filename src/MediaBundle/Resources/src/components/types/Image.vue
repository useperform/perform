<template>
<div class="perform-media-preview">
  <img :src="url" />
</div>
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
