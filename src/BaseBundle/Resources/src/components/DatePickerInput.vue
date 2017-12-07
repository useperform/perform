<template>
  <div class="input-group">
    <span class="input-group-addon" @click="shown = !shown">
      <span class="fa fa-calendar"></span>
    </span>
    <input :name="inputName" type="text" class="form-control" :value="formattedValue" />
    <div class="dropdown-menu" v-if="shown" style="display: block">
      <DatePicker :initialDate="initialPickerValue" @select="select" />
    </div>
  </div>
</template>

<script>
 import DatePicker from './DatePicker';
 import formatDate from 'date-fns/format';
 import parseDate from 'date-fns/parse';

 export default {
   props: ['inputName', 'initialValue', 'format'],
   created() {
     // transform ICU constants in format to those supported by date-fns
     this.dateFnsFormat = 'DD/MM/YYYY';

     const date = typeof this.initialValue === 'object' ?
                  this.initialValue :
                  parseDate(this.initialValue, this.dateFnsFormat, new Date());
     const isParsed = !isNaN(date.getTime());
     this.initialPickerValue = isParsed ? date : new Date();
     // only set value on the input if the initial value was provided and it was able to be parsed
     if (this.initialValue && isParsed) {
       this.value = date;
     }
   },
   data() {
     return {
       shown: false,
       initialPickerValue: null,
       value: null,
       // dateFns version of the format prop, which is ICU formatted
       dateFnsFormat: '',
     };
   },
   computed: {
     formattedValue() {
       return this.value ? formatDate(this.value, this.dateFnsFormat) : '';
     }
   },
   components: {
     DatePicker
   },
   methods: {
     select(date) {
       this.shown = false;
       this.value = date;
     }
   }
 }
</script>
