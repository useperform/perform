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

  app.func.loadSection = function(name, data, readonly) {
    if (!app.sections[name]) {
      app.func.createSection(name);
    }
    app.sections[name].removeAll();

    for (var i = 0; i < data.length; i++) {
      var type = data[i].type;
      var blockType = type.charAt(0).toUpperCase() + type.slice(1) + 'Block';
      var block = new app.models[blockType]({
        value: data[i].value,
        readonly: typeof readonly === 'undefined' ? false : Boolean(readonly)
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

  app.func.showLoadMask = function() {
    $('#load-mask').modal('show');
  };

  app.func.hideLoadMask = function() {
    $('#load-mask').modal('hide');
  };

  app.func.loadVersion = function(url) {
    var sharedSections = {};
    $('.perform-cms .shared-section').each(function() {
      var page = $(this).data('page');
      var name = $(this).data('name');
      if (!sharedSections[page]) {
        sharedSections[page] = [];
      }
      sharedSections[page].push(name);
    });
    app.func.showLoadMask();
    $.ajax({
      url: url,
      type: 'get',
      data: {
        shared: sharedSections
      },
      success: function (data) {
        _.each(data.sections, function(blocks, sectionName) {
          app.func.loadSection(sectionName, blocks);
        });
        _.each(data.sharedSections, function(sections, page) {
          _.each(sections, function(blocks, sectionName) {
            app.func.loadSection(sectionName+'__'+page, blocks, true);
          });
        });
      },
      complete: function() {
        app.func.hideLoadMask();
      }
    });
  };

  $('.perform-cms .version-selector .version').click(function(e) {
    e.preventDefault();
    app.func.loadVersion($(this).data('url'));
    var newTitle = $(this).html();
    $(this).parent().parent().find('.version').removeClass('active');
    $(this).addClass('active');
    $('.current-version-title').html(newTitle);
  });

  $('.perform-cms .version-selector .active').click();
});
