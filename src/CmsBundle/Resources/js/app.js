$(function() {
  app.sections = {};

  app.func.createSection = function(name) {
    var blocks = new Backbone.Collection();
    app.sections[name] = blocks;
    var view = new app.views.SectionView({
      collection: blocks,
      el: $('#section-' + name),
    });
    view.render();
  };

  app.func.loadSection = function(name, data) {
    var blocks = app.sections[name];
    for (var i = 0; i < data.length; i++) {
      var type = data[i].type;
      var blockType = type.charAt(0).toUpperCase() + type.slice(1) + 'Block';
      var model = new app.models[blockType]({
        id: i,
        value: data[i].value
      });

      blocks.add(model);
    }
  };

  app.func.addBlock = function(block, targetName, targetIndex) {
    app.sections[targetName].add(block, {at: targetIndex});
  };

  app.func.removeBlock = function(name, index) {
    return app.sections[name].remove(index);;
  };

  app.func.moveBlock = function(sourceName, sourceIndex, targetName, targetIndex) {
    var block = app.func.removeBlock(sourceName, sourceIndex);
    if (block) {
      app.func.addBlock(block, targetName, targetIndex);
    }
  };
});
