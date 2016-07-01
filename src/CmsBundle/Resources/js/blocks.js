$(function() {
  var app = window.app;

  app.models.ContentBlock = Backbone.Model.extend({
    defaults: function() {
      return {
        type: null,
        value: {},
      };
    },

    initialize: function() {
      this.bind('change', app.func.setDirty);
    },

    viewClass: function() {
      var type = this.get('type');
      return type.charAt(0).toUpperCase() + type.slice(1) + 'BlockView';
    },
  });

  app.views.BlockView = Backbone.View.extend({
    tagName: "div",

    //extend in child classes
    name: 'abstract',

    className: function() {
      return 'block block-' + this.name;
    },

    template: function(json) {
      return _.template($('#content-block-' + this.name).html())(json);
    },

    events: {
      "click a.edit": "edit",
      "click a.remove": "destroy",
      "click a.done": "done",
      "click a.cancel": "cancel",
    },

    initialize: function() {
      this.listenTo(this.model, 'change', this.render);
    },

    render: function() {
      this.$el.html(this.template(this.model.toJSON().value));
      this.editor = this.$('.editor');
      this.content = this.$('.content');
      this.editorInit = null;
      return this;
    },

    initEditor: function() {},

    resetEditor: function() {},

    updateContent: function() {},

    editControls: function() {
      this.$('.controls .edit').hide();
      this.$('.controls .remove').hide();
      this.$('.controls .done').show();
      this.$('.controls .cancel').show();
    },

    defaultControls: function() {
      this.$('.controls .done').hide();
      this.$('.controls .cancel').hide();
      this.$('.controls .edit').show();
      this.$('.controls .remove').show();
    },

    edit: function() {
      if (!this.editorInit) {
        this.initEditor();
        this.editorInit = true;
      }

      this.editControls();
      this.content.hide();
      this.editor.show();
    },

    cancel: function() {
      this.defaultControls();
      this.editor.hide();
      this.resetEditor();
      this.content.show();
    },

    done: function() {
      this.defaultControls();
      this.editor.hide();
      this.updateContent();
      this.content.show();
    },

    destroy: function() {
      this.model = null;
      // app.func.setDirty();
      this.remove();
    }
  });

  app.collections.Section = Backbone.Collection.extend({
    removeAt: function(index) {
      var block = this.at(index);
      if (block) {
        return this.remove(block);
      }
    },

    insertAt: function(block, index) {
      block.insertingAt = index;

      //block will go at the end if target index is not set
      return this.add(block, {at: index});
    },

    removeAll: function() {
      this.remove(this.models);
    }
  });

  app.views.SectionView = Backbone.View.extend({
    blockViews: [],

    initialize: function() {
      this.listenTo(this.collection, 'add', this.add);
      this.listenTo(this.collection, 'remove', this.remove);
    },

    add: function(block) {
      var view = new app.views[block.viewClass()]({
        model: block
      });
      this.blockViews[block.cid] = view;

      var index = block.insertingAt;
      delete block.insertingAt;

      if (typeof index !== 'undefined') {
        var existingBlocks = this.$('.block');
        if (existingBlocks.length > index) {
          return existingBlocks.eq(index).before(view.render().$el);
        }
      }
      this.$el.append(view.render().$el);
    },

    remove: function(block) {
      this.blockViews[block.cid].destroy();
      this.blockViews[block.cid] = null;
    }
  });

});
