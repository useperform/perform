<template>
  <div class="p--local">
    <div class="p-comp-rich-content-controls btn-group" role="group" aria-label="Block controls">
      <button type="button" class="btn btn-default">
        <i class="fa fa-arrows"></i>
      </button>
      <button type="button" class="btn btn-default" @click.prevent="clickUp">
        <i class="fa fa-arrow-up"></i>
      </button>
      <button type="button" class="btn btn-default" @click.prevent="clickDown">
        <i class="fa fa-arrow-down"></i>
      </button>
      <button type="button" class="btn btn-default" @click.prevent="clickRemove">
        <i class="fa fa-trash"></i>
      </button>
      <slot name="extra-buttons"></slot>
    </div>
  </div>
</template>

<script>
 export default {
   props: [
     'position',
     'editorIndex',
   ],

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
   }
 }
</script>
