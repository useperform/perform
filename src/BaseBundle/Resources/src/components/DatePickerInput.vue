<template>
  <div>
    <div class="input-group">
      <span class="input-group-addon" @click="shown = !shown">
        <span class="fa fa-calendar"></span>
      </span>
      <input :name="inputName" type="text" class="form-control" :value="formattedValue" />
    </div>
    <div class="dropdown-menu d-flex" v-if="shown" style="display: block">
      <DatePicker v-if="pickDate" :initialDate="initialPickerValue" @select="selectDate" />
      <TimePicker v-if="pickTime" :initialDate="initialPickerValue" @select="selectTime" />
    </div>
  </div>
</template>

<script>
 import DatePicker from './DatePicker';
 import TimePicker from './TimePicker';
 import formatDate from 'date-fns/format';
 import parseDate from 'date-fns/parse';
 import {ICUtoDateFns} from './../util/date';

 export default {
   props: [
     'inputName',
     'initialValue',
     'format',
     'pickDate',
     'pickTime'
   ],
   created() {
     // transform ICU format to date-fns version
     this.dateFnsFormat = ICUtoDateFns(this.format);

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
     DatePicker,
     TimePicker
   },
   methods: {
     selectDate(date) {
       this.shown = false;
       const current = this.value ? this.value : new Date();
       this.value = new Date(
         date.getFullYear(),
         date.getMonth(),
         date.getDate(),
         current.getHours(),
         current.getMinutes(),
         current.getSeconds());
     },
     selectTime(date) {
       this.shown = false;
       const current = this.value ? this.value : new Date();
       this.value = new Date(
         current.getFullYear(),
         current.getMonth(),
         current.getDate(),
         date.getHours(),
         date.getMinutes(),
         date.getSeconds());
     }
   }
 }
</script>
