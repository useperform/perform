<template>
  <div class="p-comp-timepicker d-flex flex-row align-items-start">
    <div class="input-group">
      <input class="form-control" type="text" v-model="currentInput" @change="onInputChange" />
      <button class="input-group-addon" @click.prevent="select">OK</button>
    </div>
    <!-- increments selection -->
  </div>
</template>

<script>
 import formatDate from 'date-fns/format';
 import setHours from 'date-fns/setHours';
 import setMinutes from 'date-fns/setMinutes';
 import setSeconds from 'date-fns/setSeconds';
 import {parseTimeString} from './../util/date';

 export default {
   props: ['initialDate'],
   created() {
     // set to a random date, we're only focused on time
     this.current = new Date(0, 0, 0, this.initialDate.getHours(), this.initialDate.getMinutes(), this.initialDate.getSeconds());
     this.formatInput();
   },
   data() {
     return {
       current: null,
       currentInput: null,
     };
   },
   computed: {
   },
   methods: {
     formatInput() {
       this.currentInput = formatDate(this.current, 'HH:mm');
     },
     onInputChange(e) {
       try {
         const time = parseTimeString(e.target.value);
         this.current = setHours(this.current, time.hours);
         this.current = setMinutes(this.current, time.minutes);
         this.current = setSeconds(this.current, time.seconds);
       } catch (e) {
       }
       this.formatInput();
     },
     select() {
       this.$emit('select', this.current);
     }
   }
 }
</script>
