(function () {
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

  var addTask = function(title, current, max) {
    if (!current) {
      current = 0;
    }
    if (!max) {
      max = 100;
    }
    taskapp.tasks.push({
      title: title,
      current: current,
      max: max,
    });

    return taskapp.tasks.length - 1;
  };

  var getTask = function(index) {
    return taskapp.tasks[index];
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
  window.Perform.base.addTask = addTask;
  window.Perform.base.getTask = getTask;
  window.Perform.base.setTaskProgress = setTaskProgress;
})();
