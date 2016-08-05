$(function() {
  app.sections = {};

  app.currentVersion = null;
  app.currentSection = null;
  app.currentEditor = null;

  app.func.createSection = function(name, readonly) {
    var section = new app.collections.Section();
    section.readonly = Boolean(readonly);
    app.sections[name] = section;
    var view = new app.views.SectionView({
      collection: section,
      el: $('#section-' + name),
    });
    view.render();
  };

  app.func.loadSection = function(name, data, readonly) {
    if (!app.sections[name]) {
      app.func.createSection(name, readonly);
    }
    app.sections[name].removeAll();

    for (var i = 0; i < data.length; i++) {
      var block = app.func.createBlock(data[i].type, data[i].value, readonly);
      app.func.insertBlock(block, name);
    }
  };

  app.func.createBlock = function(type, value, readonly) {
    var blockType = type.charAt(0).toUpperCase() + type.slice(1) + 'Block';
    if (!app.models[blockType]) {
      console.error('Unknown block type: '+type);
      return;
    }
    return new app.models[blockType]({
      value: value,
      readonly: typeof readonly === 'undefined' ? false : Boolean(readonly)
    });
  };

  app.func.showBlockTypePicker = function(section) {
    app.currentSection = section;
    var modal = $('#modal-perform-cms');
    var blockTypes = {};
    var editor = new app.views.AddBlockView();
    editor.render(blockTypes);
    modal.find('.modal-body').html(editor.$el);
    app.currentEditor = editor;
    modal.modal('show');
  };

  app.func.addBlock = function(type) {
    var block = app.func.createBlock(type);
    app.func.insertBlock(block, app.currentSection);
    if (app.currentEditor) {
      app.currentEditor.remove();
      app.currentEditor = null;
    }
    app.func.editBlock(block);
  };

  app.func.editBlock = function(block) {
    var modal = $('#modal-perform-cms');
    var editor = block.createEditor();
    editor.render();
    modal.find('.modal-body').html(editor.$el);
    app.currentEditor = editor;
    modal.modal('show');
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

  app.func.selectVersion = function(id) {
    var version = $('.perform-cms .version-'+id);
    if (version.length !== 1) {
      console.error('Version '+id+' not found.');
    }

    return version;
  };

  app.func.loadVersion = function(id) {
    var version = app.func.selectVersion(id);
    var url = version.data('load-url');
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
        app.currentVersion = id;

        var newTitle = version.html();
        version.parent().parent().find('.version').removeClass('active');
        version.addClass('active');
        $('.perform-cms .current-version-title').html(newTitle);
      },
      complete: function() {
        app.func.hideLoadMask();
      }
    });
  };

  app.func.writableData = function() {
    var sections = {};
    _.each(app.sections, function(section, sectionName) {
      if (section.readonly) {
        return;
      }
      sections[sectionName] = section.writableData();
    });

    return sections;
  };

  app.func.saveVersion = function(id) {
    var url = app.func.selectVersion(id).data('save-url');
    if (!url) {
      app.func.showError('An error occurred.');
      return;
    }
    $.ajax({
      url: url,
      type: 'post',
      data: {
        sections: app.func.writableData()
      },
      success: function (data) {
        app.func.showSuccess(data.message);
      },
      error: function (data) {
        app.func.showError(data.responseJSON.message);
      }
    });
  };

  app.func.publishVersion = function(id) {
    var version = $('.version-'+id);
    var url = version.data('publish-url');
    $.ajax({
      url: url,
      type: 'post',
      success: function (data) {
        app.func.showSuccess(data.message);

        var newTitle = version.html();
        version.parent().parent().find('.version .published-indicator').html('');
        version.find('.published-indicator').html('(published)');
        $('.perform-cms .current-version-title .published-indicator').html('(published)');
      },
      error: function (data) {
        app.func.showError(data.responseJSON.message);
      }
    });
  };

  $('#modal-perform-cms .modal-footer .save').click(function(e) {
    e.preventDefault();
    if (app.currentEditor) {
      app.currentEditor.updateModel();
    }
    $('#modal-perform-cms').modal('hide');
  });

  $('#modal-perform-cms').on('hidden.bs.modal', function(e) {
    if (app.currentEditor) {
      app.currentEditor.remove();
    }
  });

  $('.perform-cms .action-save').click(function(e) {
    e.preventDefault();
    app.func.saveVersion(app.currentVersion);
  });

  $('.perform-cms .add-block').click(function(e) {
    e.preventDefault();
    app.func.showBlockTypePicker($(this).data('section'));
  });

  $('.perform-cms .action-publish').click(function(e) {
    e.preventDefault();
    app.func.publishVersion(app.currentVersion);
  });

  $('.perform-cms .version-selector .version').click(function(e) {
    e.preventDefault();
    app.func.loadVersion($(this).data('id'));
  });

  $('.perform-cms .version-selector .active').click();
});
