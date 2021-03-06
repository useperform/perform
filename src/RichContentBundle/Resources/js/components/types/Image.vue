<template>
  <div v-on-clickaway="onAway">
    <block-controls v-if="active" :position="position" :editorIndex="editorIndex">
      <template slot="extra-buttons">
        <button type="button" class="btn btn-default" @click.prevent="selectImage">
          <i class="fa fa-edit"></i>
        </button>
      </template>
    </block-controls>
    <img v-if="hasImage" :src="componentInfo.src" @click.prevent="onClick" style="max-width: 100%;"/>
    <div class="p--local" v-if="missingImage">
      <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i>
        <a href="#" class="alert-link" @click.prevent="onClick">
          Missing image
        </a>
      </div>
    </div>
  </div>
</template>

<script>
 import { mixin as clickaway } from 'vue-clickaway';
 import BlockControls from '../BlockControls.vue';
 import {selectFile} from 'perform-media/src/selector';

 export default {
   props: [
     'value',
     'isNew',
     'componentInfo',
     'position',
     'editorIndex',
   ],
   data() {
     return {
       active: false,
       missing: false,
     }
   },
   mixins: [
     clickaway
   ],
   components: {
     BlockControls,
   },
   created() {
     if (!this.hasImage && this.isNew) {
       this.selectImage();
     }
   },
   computed: {
     hasImage() {
       return !!this.componentInfo.src;
     },
     // when an image id has been set, but it has been moved or deleted
     missingImage() {
       return !!this.componentInfo.missing;
     },
   },
   methods: {
     onClick() {
       this.active = true;
     },
     onAway() {
       this.active = false;
     },
     selectImage() {
       const that = this;
       selectFile({
         onSelect(files) {
           const file = files[0];
           that.$emit('update', {
             id: file.id,
           }, {
             src: file.url,
             missing: false,
           });
         }
       });
     }
   }
 }
</script>
