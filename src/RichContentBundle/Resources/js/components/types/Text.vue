<template>
  <div ref="container">
    <block-controls v-if="active" :position="position" :editorIndex="editorIndex" />
    <div class="p-rich-text" ref="content"></div>
  </div>
</template>

<script>
 import MediumEditor from 'medium-editor';
 import BlockControls from '../BlockControls.vue';
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
       options: {
         toolbar: {
           buttons: [
             'bold',
             'italic',
             'underline',
             'anchor',
             'h2',
             'h3',
             'quote',
             'unorderedlist',
           ],
         }
       }
     };
   },

   components: {
     BlockControls,
     MediumEditor,
   },

   mounted() {
     this.editor = new MediumEditor(this.$refs.content, this.options);
     if (this.value.content) {
       this.editor.setContent(this.value.content);
     }

     let that = this;
     this.editHandler = debounce(function(event) {
       // medium editor occasionally sends bad events
       if (event.srcElement === undefined) {
         return;
       }
       const value = event.srcElement.innerHTML;
       if (!value) {
         return;
       }
       that.$emit('update', {content: value});
     }, 2000);
     this.editor.subscribe('editableInput', this.editHandler);
     this.editor.subscribe('focus', (event) => {
       that.active = true;
     });

     this.editor.subscribe('externalInteraction', (event) => {
       // hide the controls when clicking away from the editor or block controls
       if (!this.$refs.container.contains(event.target)) {
         that.active = false;
       }
     });
   },

   beforeDestroy () {
     this.editor.unsubscribe('editableInput', this.editHandler);
     this.editor.destroy();
   },
 }
</script>
