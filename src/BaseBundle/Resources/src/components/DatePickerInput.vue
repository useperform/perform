<template>
  <div>
    <div class="input-group">
      <div class="input-group-prepend" @click="shown = !shown">
        <span class="input-group-text">
          <i class="fa fa-calendar"></i>
        </span>
      </div>
      <flat-pickr :name="inputName" :disabled="disabled" class="form-control" autocomplete="off" v-model="datetime" :config="flatPickrConfig" />
    </div>
  </div>
</template>

<script>
 import flatPickr from 'vue-flatpickr-component';

 export default {
   props: [
     'inputName',
     'disabled',
     'initialValue',
     'flatPickrConfig',
   ],
   data() {
     return {
       datetime: this.initialValue,
     };
   },
   components: {
     flatPickr
   },
 }
</script>
