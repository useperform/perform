<template>
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle" href="#" id="perform-tasks-dropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    <i class="fa fa-tasks"></i>
    <span class="badge badge-primary" v-show="unfinished.length > 0">{{unfinished.length}}</span>
  </a>
  <div class="dropdown-menu" aria-labelledby="perform-tasks-dropdown">
    <b-dropdown-header>Running tasks</b-dropdown-header>
    <template v-for="task in unfinished">
      <transition name="fade">
        <b-dropdown-item href="#">
          <p>{{task.title}}</p>
          <b-progress :value="task.current" :max="task.max" animated></b-progress>
        </b-dropdown-item>
      </transition>
    </template>
  </div>
</li>
</template>

<script>
import bDropdownHeader from 'bootstrap-vue/es/components/dropdown/dropdown-header'
import bDropdownItem from 'bootstrap-vue/es/components/dropdown/dropdown-item'
import bProgress from 'bootstrap-vue/es/components/progress/progress'

export default {
  props: ['tasks'],
  components: {
    'b-dropdown-header': bDropdownHeader,
    'b-dropdown-item': bDropdownItem,
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
