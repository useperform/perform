<template>
  <div class="block">
    <div class="actions">
      <a class="btn btn-default">
        <i class="fa fa-arrows"></i>
      </a>
      <a class="btn btn-default" :click.prevent="clickUp">
        <i class="fa fa-arrow-up"></i>
      </a>
      <a class="btn btn-default" :click.prevent="clickDown">
        <i class="fa fa-arrow-down"></i>
      </a>
      <a class="btn btn-default" :click.prevent="clickEdit">
        <i :class="editClass"></i>
      </a>
      <a class="btn btn-default" :click.prevent="clickRemove">
        <i class="fa fa-trash"></i>
      </a>
    </div>
    <component :is="blockType" :value="block.value" :editing="editing" :setBlockValue="setBlockValue" />
  </div>
</template>

<script>
 import blockTypes from './blocktypes';

 export default {
   props: ['position', 'block'],

   data() {
     return {
       editing: false,
     };
   },

   methods: {
     clickEdit() {
       this.editing = !this.state.editing;
     },

     clickUp() {
       this.$store.dispatch('moveBlock',
         [this.props.editorIndex, this.props.position],
         [this.props.editorIndex, this.props.position - 1]
       );
     },

     clickDown() {
       this.$store.dispatch('moveBlock',
         [this.props.editorIndex, this.props.position],
         [this.props.editorIndex, this.props.position + 1]
       );
     },

     clickRemove() {
       this.$store.dispatch('removeBlock', [this.props.editorIndex, this.props.position]);
     },

     setBlockValue(value) {
       this.editing = false;
       this.$store.commit('BLOCK_UPDATE', {
         id: this.block.id,
         value: this.block.value
       });
     },
   },

   computed: {
     editClass() {
       return 'fa ' + (this.editing ? 'fa-times' : 'fa-pencil');
     },
     blockType() {
       return blockTypes[this.block.type].class;
     }
   }

 }
</script>
