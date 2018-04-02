<template>
  <div class="toolbar">
    <a class="btn btn-primary btn-xs" href="#" @click.prevent="save" :disabled="saving">
      Save
    </a>
  </div>
</template>

<script>
 export default {
   props: ['editorIndex'],

   data() {
     return {
       saving: false,
     };
   },

   computed: {
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
   }
 }
</script>
