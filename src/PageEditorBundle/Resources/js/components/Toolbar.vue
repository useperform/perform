<template>
  <div class="p--local">
    <div class="p-comp-page-editor-toolbar">
      <a class="btn btn-primary" @click.prevent="save">
        Save
      </a>
      <a class="btn btn-light" :href="finishUrl">
        Finish editing
      </a>
    </div>
  </div>
</template>

<script>
 export default {
   props: [
     'finishUrl',
   ],

   methods: {
     save() {
       this.$store.dispatch('save');
     }
   }
 };
</script>
