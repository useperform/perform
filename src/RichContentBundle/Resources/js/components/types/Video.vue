<template>
  <div>
    <div>
      <input type="text" class="form-control" @input="onInputChange" />
    </div>
    <div v-html="embed"></div>
  </div>
</template>

<script>
 import getVideoId from 'get-video-id';
 import debounce from 'lodash.debounce';

 export default {
   props: ['value', 'setBlockValue'],
   methods: {
     onInputChange(e) {
       this.updateUrl(e.currentTarget.value);
     },

     /* updateUrl: debounce(url => {*/
     /* const res = getVideoId(url);*/
     /* if (!res) {*/
     /* this.updateValue(false, false);*/
     /* return;*/
     /* }*/
     /* this.updateValue(res.service, res.id);*/
     /* }, 300),*/
     updateUrl(url) {
       const res = getVideoId(url);
       if (!res) {
         this.updateValue(false, false);
         return;
       }
       this.updateValue(res.service, res.id);
     },

     updateValue(type, id) {
       this.setBlockValue({
         type,
         id
       });
     }
   },

   computed: {
     embed() {
       switch (this.value.type) {
         case 'youtube':
           return `<iframe src=https://www.youtube.com/embed/${this.value.id} width="560" height="315" frameBorder="0" allowFullScreen></iframe>`;
         case 'vimeo':
           return `<iframe src="https://player.vimeo.com/video/${this.value.id}" width="560" height="315" frameBorder="0" allowFullScreen></iframe>`;
       }
     }
   },
 }
</script>
