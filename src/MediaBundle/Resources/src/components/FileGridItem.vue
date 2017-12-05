<template>
<div class="card perform-media-grid-item" :class="{selected}" @click="click" @mouseover="hover">
  <component :is="previewComponent" :filename="file.filename" class="card-img-top"/>
  <div class="card-body">
    <p class="card-text">
      {{file.name}}
    </p>
  </div>
</div>
</template>

<script>
import Image from './types/Image';
import Other from './types/Other';

export default {
  props: ['file', 'selected'],
  data () {
    return {
      hovering: false,
    }
  },
  computed: {
    previewComponent() {
      if (this.file.type === 'image') {
        return Image;
      }
      return Other;
    }
  },
  methods: {
    click() {
      this.$emit('toggleSelect');
    },
    hover() {
      this.hovering = !this.hovering;
    }
  }
}
</script>
