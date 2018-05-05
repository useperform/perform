<template>
  <div v-on-clickaway="onAway">
    <block-controls v-if="active" :position="position" :editorIndex="editorIndex">
      <template slot="extra-buttons">
        <button type="button" class="btn btn-default" @click.prevent="edit">
          <i class="fa fa-edit"></i>
        </button>
      </template>
    </block-controls>
    <div class="p--local">
      <div v-if="editing">
        <div class="form-group row">
          <label for="quote" class="col-sm-2 offset-sm-1 col-form-label">Quote</label>
          <div class="col-sm-8">
            <textarea v-model="text" class="form-control"></textarea>
          </div>
        </div>
        <div class="form-group row">
          <label for="author" class="col-sm-2 offset-sm-1 col-form-label">
            Author
          </label>
          <div class="col-sm-8">
            <input type="text" v-model="cite" class="form-control" />
          </div>
        </div>
        <a class="btn btn-primary" href="#" @click.prevent="update">Update</a>
      </div>
    </div>
    <blockquote @click="onClick" v-if="!editing">
      <p v-if="text">
        {{text}}
      </p>
      <p v-else>
        <i>Once upon a time...</i>
      </p>
      <footer v-if="cite">
        <cite>
          {{cite}}
        </cite>
      </footer>
    </blockquote>
  </div>
</template>

<script>
 import { mixin as clickaway } from 'vue-clickaway';
 import BlockControls from '../BlockControls.vue';

 export default {
   props: [
     'value',
     'position',
     'editorIndex',
   ],
   data() {
     return {
       text: null,
       cite: null,
       active: false,
       editing: false,
     }
   },
   mixins: [
     clickaway
   ],
   components: {
     BlockControls,
   },
   created() {
     this.text = this.value.text;
     this.cite = this.value.cite;
   },
   methods: {
     onClick() {
       this.active = true;
     },
     onAway() {
       this.active = false;
       this.editing = false;
     },
     edit() {
       this.editing = true;
     },
     update() {
       this.$emit('update', {
         text: this.text,
         cite: this.cite,
       });
       this.editing = false;
     }
   }
 }
</script>
