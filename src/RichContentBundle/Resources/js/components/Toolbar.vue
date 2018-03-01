<template>
  <div class="toolbar">
    <a class="btn btn-primary btn-xs" href="#" @click.prevent="save" :disabled="saving">
      Save
    </a>
    <a class="btn btn-primary btn-xs" href="#" @click.prevent="chooseBlock" :disabled="saving">
      {{choosing ? 'Cancel' : 'Add block'}}
    </a>
    <div class="block-selector" :style="this.choosing ? {} : {display: 'none'}">
      <ul>
        <li v-for="type, typeName in blockTypes" @click.prevent="addBlock(typeName)" @mouseenter="onHoverStart(typeName)" @mouseleave="onHoverStop" :key="typeName">
          {{type.name}}
        </li>
      </ul>
      <div class="description">
        <p>
          {{description}}
        </p>
      </div>
      <div class="clear"></div>

    </div>
  </div>
</template>

<script>
 import blockTypes from './blocktypes';

 export default {
   props: ['editorIndex'],

   data() {
     return {
       saving: false,
       choosing: false,
       blockChoice: false,
       blockTypes,
     };
   },

   computed: {
     description() {
       return this.blockChoice ? blockTypes[this.blockChoice].description : ''
     }
   },

   methods: {
     save(e) {
       this.saving = true;
       this.choosing = false
       this.$store.dispatch('save', {
         editorIndex: this.editorIndex,
         onSuccess: this.notSaving,
         onError: this.notSaving
       });
     },

     notSaving() {
       this.saving = false;
     },

     addBlock(type) {
       this.choosing = false;
       this.blockChoice = false;
       this.$store.commit('BLOCK_ADD', {
         blockType: type,
         editorIndex: this.editorIndex
       });
     },

     chooseBlock(e) {
       this.choosing = !this.choosing;
     },

     onHoverStart(type) {
       this.blockChoice = type;
     },

     onHoverStop() {
       this.blockChoice = false;
     }
   }
 }
</script>
