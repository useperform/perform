import TaskList from './components/TaskList'
import TaskCounter from './components/TaskCounter'
import Vue from 'vue'

const store = {
  tasks: [],
};

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

new Vue({
  el: '#p-tasks-dropdown',
  data: store,
  render(h) {
    return h(TaskList, {props: {tasks: this.tasks}});
  }
});
new Vue({
  el: '#p-tasks-counter',
  data: store,
  render(h) {
    return h(TaskCounter, {props: {tasks: this.tasks}});
  }
});

window.addEventListener('beforeunload', function(e) {
  if (getUnfinished().length > 0) {
    var confirmationMessage = "You have unfinished tasks. Leave this page?";
    e.returnValue = confirmationMessage;
    return confirmationMessage;
  }
});
