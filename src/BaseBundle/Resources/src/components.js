import TaskList from './components/TaskList'
import TaskCounter from './components/TaskCounter'
import DatePickerInput from './components/DatePickerInput'
import Vue from 'vue'
import md from './util/markdown';

const store = {
  tasks: [],
};

const renderDropdown = function(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskList, {props: {tasks: this.tasks}});
    }
  });
}

const renderCounter = function(el) {
  new Vue({
    el: el,
    data: store,
    render(h) {
      return h(TaskCounter, {props: {tasks: this.tasks}});
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

const datepicker = function(el, opts) {
  new Vue({
    el: el,
    render(h) {
      return h(DatePickerInput, {props: opts});
    }
  });
};

const markdown = function(input, preview) {
  input.on('keyup', function(e) {
    preview.html(md.render(e.currentTarget.value));
  });
  preview.html(md.render(input.val()));
};

if (!window.Perform) {
  window.Perform = {};
}
if (!window.Perform.base) {
  window.Perform.base = {};
}
window.Perform.base = Object.assign({}, window.Perform.base, {
  tasks: {
    renderCounter,
    renderDropdown,
    add,
    get,
    getUnfinished,
    setProgress,
    cancel,
  },
  form: {
    datepicker,
    markdown
  }
});
