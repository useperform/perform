<template>
  <div class="block-list">
    <Block v-for="block, i in sortedBlocks" :key="block.id" :block="block" :position="i" :editorIndex="editorIndex" />
    <AddButton :editorIndex="editorIndex" />
  </div>
</template>

<script>
 import Block from './Block';
 import AddButton from './AddButton';

 export default {
   props: ['editorIndex'],

   components: {
     Block,
     AddButton,
   },

   computed: {
     sortedBlocks() {
       const order = this.$store.state.editors[this.editorIndex].order;
       let blocks = [];

       for (let i=0; i < order.length; i++) {
         let id = order[i][0];
         let block = this.$store.state.blocks[id];
         if (!block) {
           throw Error(`Unknown block ${id}`);
         }
         block.id = id;
         block.key = order[i][1];
         blocks.push(block);
       }

       return blocks;
     }
   }
 }
</script>
