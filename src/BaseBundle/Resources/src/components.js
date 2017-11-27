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
  const id = Math.random().toString().substring(2);
  store.tasks.push({
    id,
    title,
    current,
    max,
  });

  return id;
};

const get = function(id) {
  for (var i=0; i < store.tasks.length; i++) {
    if (store.tasks[i].id === id) {
      return store.tasks[i];
    }
  }
};

const getUnfinished = function() {
  return store.tasks.filter(function(task) {
    return task.current < task.max;
  });
}

const setProgress = function(id, current) {
  get(id).current = current;
};

const cancel = function(id) {
  store.tasks = store.tasks.filter(function(task) {
    return task.id !== id;
  });
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
    cancel,
  }
});
