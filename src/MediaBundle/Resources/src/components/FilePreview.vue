<template>
  <div class="p-media-preview">
    <component :is="previewComponent" :file="file" />
  </div>
</template>

<script>
import Image from './types/Image';
import Other from './types/Other';

 export default {
   props: ['file'],
   computed: {
    previewComponent() {
      if (this.file.type === 'image') {
        return Image;
      }
      return Other;
    }
   },

 }
</script>
