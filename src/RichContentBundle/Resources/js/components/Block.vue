<template>
  <component
    :is="blockType"
    :value="block.value"
    :componentInfo="block.component_info"
    :editorIndex="editorIndex"
    :position="position"
    :isNew="isNew"
    @update="setBlockValue"
  />
</template>

<script>
 import blockTypes from './blocktypes';

 export default {
   props: [
     'block',
     'position',
     'editorIndex',
   ],

   methods: {
     setBlockValue(value, info) {
       this.$store.commit('BLOCK_UPDATE', {
         id: this.block.id,
         value,
         info,
       });
     },
   },

   computed: {
     blockType() {
       return blockTypes[this.block.type].class;
     },
     isNew() {
       return this.block.id.substr(0, 1) === '_';
     }
   }

 }
</script>
