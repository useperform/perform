<template>
<div class="dropdown-menu dropdown-menu-right" aria-labelledby="perform-tasks-dropdown">
  <span class="dropdown-item" v-if="unfinished.length < 1">
    No running tasks
  </span>
  <transition name="fade" v-for="task in unfinished" :key="task.id">
    <div class="dropdown-item">
      <span>{{task.title}}</span>
      <b-progress height="5px" :value="task.current" :max="task.max" animated></b-progress>
    </div>
  </transition>
</div>
</template>

<script>
import bProgress from 'bootstrap-vue/es/components/progress/progress'

export default {
  props: ['tasks'],
  components: {
    'b-progress': bProgress,
  },
  computed: {
    unfinished () {
      return this.tasks.filter(function(task) {
        return task.current < task.max;
      });
    }
  }
}
</script>
