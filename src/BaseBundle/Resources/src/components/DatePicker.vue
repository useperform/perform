<template>
  <table class="table-condensed">
    <thead>
      <tr>
        <th @click="subMonth">«</th>
        <th colspan="5">{{currentMonthYearName}}</th>
        <th @click="addMonth">»</th>
      </tr>
      <tr>
        <th v-for="day in ['Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa', 'Su']">
          {{day}}
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="week in currentDays">
        <td v-for="date in week" @click="select(date)">
          {{date.getDate()}}
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
 import subDays from 'date-fns/sub_days';
 import addDays from 'date-fns/add_days';
 import subMonths from 'date-fns/sub_months';
 import addMonths from 'date-fns/add_months';
 import startOfMonth from 'date-fns/start_of_month';
 import formatDate from 'date-fns/format';

 export default {
   props: ['initialDate'],
   created() {
     // set the hour to mid-day to avoid any problems with daylight savings when adding days and months
     this.current = new Date(this.initialDate.getFullYear(), this.initialDate.getMonth(), 1, 12);
   },
   data() {
     return {
       current: null,
     };
   },
   computed: {
     currentDays() {
       let cursor = startOfMonth(this.current);
       if (cursor.getDay() !== 1) {
         cursor = subDays(cursor, cursor.getDay() - 1);
       }
       let rows = [];
       const rowCount = 6;
       const cellCount = 7;
       for (let row = 0; row < rowCount; row++) {
         rows.push([]);
         for (let cell = 0; cell < cellCount; cell++) {
           rows[row].push(new Date(cursor.getTime()));
           cursor = addDays(cursor, 1);
         }
       }

       return rows;
     },
     currentMonthYearName() {
       return formatDate(this.current, 'MMMM YYYY');
     }
   },
   methods: {
     addMonth() {
       this.current = addMonths(this.current, 1);
     },
     subMonth() {
       this.current = subMonths(this.current, 1);
     },
     select(date) {
       this.$emit('select', date);
     }
   }
 }
</script>
