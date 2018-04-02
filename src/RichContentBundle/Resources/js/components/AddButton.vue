<template>
  <div v-on-clickaway="onAway" class="add-button">
    <a class="add" href="#" @click.prevent="toggleChoosing" :title="linkDescription">
      <i v-if="!this.choosing" class="fa fa-plus"></i>
      <i v-if="this.choosing" class="fa fa-minus"></i>
    </a>
    <div class="type-tooltip" v-if="this.choosing">
      <input type="text" v-model="filterText" ref="inputFilter"/>
      <ul>
        <li v-for="type, typeName in filteredBlockTypes" @click.prevent="addBlock(typeName)" @mouseenter="onHoverStart(typeName)" @mouseleave="onHoverStop" :key="typeName">
          <div class="name">
            {{type.name}}
          </div>
          <p class="description">
            {{type.description}}
          </p>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
 import { mixin as clickaway } from 'vue-clickaway';
 import blockTypes from './blocktypes';
 import Vue from 'vue';

 export default {
   props: [
     'editorIndex'
   ],

   data() {
     return {
       choosing: false,
       blockChoice: false,
       filterText: '',
     }
   },

   mixins: [
     clickaway
   ],

   computed: {
     linkDescription() {
       return this.choosing ? 'Cancel' : 'Add a piece of content'
     },
     typeDescription() {
       return this.blockChoice ? blockTypes[this.blockChoice].description : ''
     },
     filteredBlockTypes() {
       if (this.filterText.length < 1) {
         return blockTypes;
       }

       return Object.keys(blockTypes)
                    .filter((key) => {
                      return blockTypes[key].name.toLowerCase().indexOf(this.filterText) > -1;
                    })
                    .reduce((result, key) => {
                      result[key] = blockTypes[key];
                      return result;
                    }, {});
     }
   },

   methods: {
     addBlock(type) {
       this.choosing = false;
       this.blockChoice = false;
       this.$store.commit('BLOCK_ADD', {
         blockType: type,
         editorIndex: this.editorIndex
       });
     },

     toggleChoosing() {
       this.choosing = !this.choosing;
       if (this.choosing) {
         const refs = this.$refs;
         Vue.nextTick(function () {
           refs.inputFilter.focus();
         });
       }
     },

     onAway() {
       console.log('away');
       this.choosing = false;
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
