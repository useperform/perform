<template>
  <div class="p-rich-editor">
    <Toolbar :editorIndex="editorIndex" v-if="showToolbar" />
    <i class="fa fa-2x fa-spin fa-spinner" v-if="loading"></i>
    <BlockList :editorIndex="editorIndex" />
  </div>
</template>

<script>
 import BlockList from './BlockList';
 import Toolbar from './Toolbar';
 import css from './editor.scss';

 export default {
   props: [
     'editorIndex',
     'showToolbar',
   ],

   data() {
     return {
     }
   },

   components: {
     BlockList,
     Toolbar
   },

   computed: {
     loading() {
       return this.$store.state.editors[this.editorIndex].loading;
     }
   }
 }
</script>
