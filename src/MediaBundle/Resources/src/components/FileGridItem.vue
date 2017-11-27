<template>
<div class="perform-media-grid-item" @click="click" @mouseover="hover">
  <component :is="previewComponent" :filename="filename" />
  <p>{{name}}</p>
  <span v-if="selected">X</span>
  <span v-if="hovering">H</span>
</div>
</template>

<script>
import Image from './types/Image';
import Other from './types/Other';

export default {
  props: ['name', 'id', 'type', 'humanType', 'filename', 'selected'],
  data () {
    return {
      hovering: false,
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
    click() {
      this.$store.dispatch('toggleSelect', this.id);
    },
    hover() {
      this.hovering = !this.hovering;
    }
  }
}
</script>
