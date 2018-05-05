<template>
  <div>
    <a class="btn btn-secondary btn-sm" href="#" @click="select">
      Select file
    </a>
    <FilePreview v-if="hasFile" :file="file"/>
  </div>
</template>

<script>
 import {selectFile} from '../selector';
 import FilePreview from './FilePreview';

 export default {
   props: [
     'inputSelector',
     'initialFile',
   ],
   data() {
     return {
       file: null,
     }
   },
   created() {
     if (this.initialFile) {
       this.file = this.initialFile;
     }
   },
   components: {
     FilePreview
   },
   methods: {
     select() {
       selectFile({
         onSelect: (files) => {
           this.setFile(files[0]);
         }
       });
     },
     setFile(file) {
       document.querySelector(this.inputSelector).value = file.id;
       this.file = file;
     }
   },
   computed: {
     hasFile() {
       return !!this.file.id;
     }
   }
 }
</script>
