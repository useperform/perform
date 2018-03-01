<template>
  <div>
    <p v-if="this.editing">
      {{this.value.content}}
    </p>
    <div v-else>
      <textarea v-model="content" rows="10" cols="80"></textarea>
      <a href="#" className="btn btn-info" :click.prevent="finishEdit">Done</a>
    </div>
  </div>
</template>

<script>
 export default {
   props: ['value', 'setBlockValue'],

   created() {
     this.content = this.value.content;
   },
   data() {
     return {
       editing: true,
     }
   },
   methods: {
     finishEdit(e) {
       this.setBlockValue({
         content: this.content
       });
     }
   }
 }
</script>
