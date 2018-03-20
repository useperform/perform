<template>
  <div class="p-rich-editor">
    <Toolbar :editorIndex="editorIndex" />
    <BlockList :editorIndex="editorIndex" />
  </div>
</template>

<script>
 import BlockList from './BlockList';
 import Toolbar from './Toolbar';
 import css from './editor.scss';

 export default {
   props: ['editorIndex'],

   data() {
     return {
     }
   },

   components: {
     BlockList,
     Toolbar
   }
 }
</script>
