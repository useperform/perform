<template>
  <div ref="content"></div>
</template>

<script>
 import MediumEditor from 'medium-editor';
 import debounce from 'lodash.debounce';

 export default {
   props: [
     'value',
   ],

   data() {
     return {
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
   },

   beforeDestroy () {
     this.editor.unsubscribe('editableInput', this.editHandler);
     this.editor.destroy();
   },
 }
</script>
