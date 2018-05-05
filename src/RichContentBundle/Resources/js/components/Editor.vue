<template>
  <div>
    <Toolbar :editorIndex="editorIndex" v-if="showToolbar" />
    <div class="p--local">
      <i class="fa fa-2x fa-spin fa-spinner" v-if="loading"></i>
    </div>
    <BlockList :editorIndex="editorIndex" />
  </div>
</template>

<script>
 import BlockList from './BlockList';
 import Toolbar from './Toolbar';

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
