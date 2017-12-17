<template>
<div class="card perform-media-grid-item" :class="{selected}" @click="click" @mouseover="hover">
  <FilePreview :file="file" class="card-img-top"/>
  <div class="card-body">
    <p class="card-text">
      {{file.name}}
    </p>
  </div>
</div>
</template>

<script>
import FilePreview from './FilePreview'

export default {
  props: ['file', 'selected'],
  data () {
    return {
      hovering: false,
    }
  },
  methods: {
    click() {
      this.$emit('toggleSelect');
    },
    hover() {
      this.hovering = !this.hovering;
    }
  },
  components: {
    FilePreview
  }
}
</script>
