import TaskList from './components/TaskList'
import TaskCounter from './components/TaskCounter'
import Vue from 'vue'

const store = {
  tasks: [],
};

export function renderDropdown(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskList, {props: {tasks: this.tasks}});
    }
  });
}

export function renderCounter(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskCounter, {props: {tasks: this.tasks}});
    }
  });
}

export function add(title, current, max) {
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

export function get(id) {
  for (var i=0; i < store.tasks.length; i++) {
    if (store.tasks[i].id === id) {
      return store.tasks[i];
    }
  }
};

export function getUnfinished() {
  return store.tasks.filter(function(task) {
    return task.current < task.max;
  });
}

export function setProgress(id, current) {
  get(id).current = current;
};

export function cancel(id) {
  store.tasks = store.tasks.filter(function(task) {
    return task.id !== id;
  });
};
