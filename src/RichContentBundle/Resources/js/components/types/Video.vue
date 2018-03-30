<template>
  <div ref="container">
    <block-controls v-if="active" :position="position" :editorIndex="editorIndex" />
    <input type="text" class="form-control" @input="inputHandler" @focus="onFocus" @blur="onBlur"/>
    <div v-html="embed"></div>
  </div>
</template>

<script>
 import BlockControls from '../BlockControls.vue';
 import getVideoId from 'get-video-id';
 import debounce from 'lodash.debounce';

 export default {
   props: [
     'value',
     'position',
     'editorIndex',
   ],

   data() {
     return {
       active: false,
     };
   },

   components: {
     BlockControls,
   },

   methods: {
     onFocus() {
       this.active = true;
     },
     onBlur() {
       if (!this.$refs.container.contains(event.target)) {
         this.active = false;
       }
     },
     inputHandler: debounce(function(event) {
       const res = getVideoId(event.target.value);
       if (!res) {
         this.$emit('update', {});
         return;
       }
       this.$emit('update', {
         type: res.service,
         id: res.id,
       });
     }, 500),
   },

   computed: {
     embed() {
       switch (this.value.type) {
         case 'youtube':
           return `<iframe src=https://www.youtube.com/embed/${this.value.id} width="560" height="315" frameBorder="0" allowFullScreen></iframe>`;
         case 'vimeo':
           return `<iframe src="https://player.vimeo.com/video/${this.value.id}" width="560" height="315" frameBorder="0" allowFullScreen></iframe>`;
       }
     }
   },
 }
</script>
