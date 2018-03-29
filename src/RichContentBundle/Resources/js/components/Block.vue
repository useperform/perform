<template>
  <div class="block">
    <component
      :is="blockType"
      :value="block.value"
      :editorIndex="editorIndex"
      :position="position"
      @update="setBlockValue"
    />
  </div>
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
     setBlockValue(value) {
       this.$store.commit('BLOCK_UPDATE', {
         id: this.block.id,
         value: value
       });
     },
   },

   computed: {
     blockType() {
       return blockTypes[this.block.type].class;
     }
   }

 }
</script>
