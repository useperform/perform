<template>
  <span class="badge badge-primary" v-show="unfinished.length > 0">{{unfinished.length}}</span>
</template>

<script>
export default {
  props: ['tasks'],
  computed: {
    unfinished () {
      return this.tasks.filter(function(task) {
        return task.current < task.max;
      });
    }
  }
}
</script>
