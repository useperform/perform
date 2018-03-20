<template>
  <div class="block">
    <div class="actions" v-if="editing">
      <a class="btn btn-default">
        <i class="fa fa-arrows"></i>
      </a>
      <a class="btn btn-default" @click.prevent="clickUp">
        <i class="fa fa-arrow-up"></i>
      </a>
      <a class="btn btn-default" @click.prevent="clickDown">
        <i class="fa fa-arrow-down"></i>
      </a>
      <a class="btn btn-default" @click.prevent="clickRemove">
        <i class="fa fa-trash"></i>
      </a>
    </div>
    <component :is="blockType" :value="block.value" @update="setBlockValue" />
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

   data() {
     return {
       // change to a prop that is updated when the current block is being edited - contenteditable has focus, image is clicked, etc
       editing: true,
     };
   },

   methods: {
     clickUp() {
       this.$store.commit('BLOCK_MOVE', {
         from: [this.editorIndex, this.position],
         to: [this.editorIndex, this.position - 1],
       });
     },

     clickDown() {
       this.$store.commit('BLOCK_MOVE', {
         from: [this.editorIndex, this.position],
         to: [this.editorIndex, this.position + 1],
       });
     },

     clickRemove() {
       this.$store.commit('BLOCK_REMOVE', {
         editorIndex: this.editorIndex,
         position: this.position,
       });
     },

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
