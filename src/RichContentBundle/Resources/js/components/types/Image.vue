<template>
  <div v-on-clickaway="onAway">
    <block-controls v-if="active" :position="position" :editorIndex="editorIndex">
      <template slot="extra-buttons">
        <button type="button" class="btn btn-default" @click.prevent="selectImage">
          <i class="fa fa-edit"></i>
        </button>
      </template>
    </block-controls>
    <img v-if="hasImage" :src="value.src" @click.prevent="onClick" style="max-width: 100%;"/>
  </div>
</template>

<script>
 import { mixin as clickaway } from 'vue-clickaway';
 import BlockControls from '../BlockControls.vue';

 export default {
   props: [
     'value',
     'position',
     'editorIndex',
   ],
   data() {
     return {
       active: false,
     }
   },
   mixins: [
     clickaway
   ],
   components: {
     BlockControls,
   },
   created() {
     if (!this.hasImage()) {
       this.selectImage();
     }
   },
   methods: {
     onClick() {
       this.active = true;
     },
     onAway() {
       this.active = false;
     },
     hasImage() {
       return !!this.value.src;
     },
     selectImage() {
       var that = this;
       Perform.media.selectFile({
         onSelect(files) {
           that.$emit('update', {
             src: files[0].url,
           });
         }
       });
     }
   }
 }
</script>
