$(function() {
  app.sections = {};

  app.func.createSection = function(name) {
    var section = new app.collections.Section();
    app.sections[name] = section;
    var view = new app.views.SectionView({
      collection: section,
      el: $('#section-' + name),
    });
    view.render();
  };

  app.func.loadSection = function(name, data) {
    for (var i = 0; i < data.length; i++) {
      var type = data[i].type;
      var blockType = type.charAt(0).toUpperCase() + type.slice(1) + 'Block';
      var block = new app.models[blockType]({
        value: data[i].value
      });

      app.func.insertBlock(block, name);
    }
  };

  app.func.insertBlock = function(block, targetName, targetIndex) {
    app.sections[targetName].insertAt(block, targetIndex);
  };

  app.func.removeBlock = function(name, index) {
    return app.sections[name].removeAt(index);
  };

  app.func.moveBlock = function(sourceName, sourceIndex, targetName, targetIndex) {
    var block = app.func.removeBlock(sourceName, sourceIndex);
    if (block) {
      app.func.insertBlock(block, targetName, targetIndex);
    }
  };
});
