import TaskList from './components/TaskList'
import Vue from 'vue'

const store = {
  tasks: [],
};

const initTasks = function(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskList, {props: {tasks: this.tasks}});
    }
  });
}

var addTask = function(title, current, max) {
  if (!current) {
    current = 0;
  }
  if (!max) {
    max = 100;
  }
  store.tasks.push({
    title: title,
    current: current,
    max: max,
  });

  return store.tasks.length - 1;
};

var getTask = function(index) {
  return store.tasks[index];
};

var setTaskProgress = function(index, current) {
  getTask(index).current = current;
};

if (!window.Perform) {
  window.Perform = {};
}
if (!window.Perform.base) {
  window.Perform.base = {};
}
window.Perform.base = Object.assign(window.Perform.base, {
  initTasks,
  addTask,
  getTask,
  setTaskProgress,
});
