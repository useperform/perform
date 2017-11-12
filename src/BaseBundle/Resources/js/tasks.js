var Tasks = {
  props: ['tasks'],
  data: function () {
    return {
      tasks: [],
    };
  },
  template: '#template-tasks'
};

var taskapp = new Vue({
  el: '#perform-tasks',
  data: {
    tasks: []
  },
  components: {
    'p-tasks': Tasks,
  }
});

window.Perform = window.Perform ? window.Perform : {};
window.Perform.func = window.Perform.func ? window.Perform.func : {};

window.Perform.func.addTask = function(title, current, progress) {
  taskapp.tasks.push({
    title: title,
    current: current,
    progress: progress,
  });
};
