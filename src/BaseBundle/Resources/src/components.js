import TaskList from './components/TaskList'
import Vue from 'vue'

const store = {
  tasks: [],
};

const init = function(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskList, {props: {tasks: this.tasks}});
    }
  });
}

const add = function(title, current, max) {
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

const get = function(index) {
  return store.tasks[index];
};

const getUnfinished = function() {
  return store.tasks.filter(function(task) {
    return task.current < task.max;
  });
}

const setProgress = function(index, current) {
  get(index).current = current;
};

if (!window.Perform) {
  window.Perform = {};
}
if (!window.Perform.base) {
  window.Perform.base = {};
}
window.Perform.base = Object.assign(window.Perform.base, {
  tasks: {
    init,
    add,
    get,
    getUnfinished,
    setProgress,
  }
});
