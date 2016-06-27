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
      this.bind('change', app.func.setDirty)
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
      this.listenTo(this.model, 'destroy', this.remove);
    },

    render: function() {
      this.$el.html(this.template(this.model.toJSON().value));
      this.editor = this.$('.content-block-editor');
      this.content = this.$('.content-block-content');
      this.editorInit = null;
      return this;
    },

    initEditor: function() {},

    resetEditor: function() {},

    updateContent: function() {},

    edit: function() {
      if (!this.editorInit) {
        this.initEditor();
        this.editorInit = true;
      }

      this.content.hide();
      this.editor.show();
    },

    cancel: function() {
      this.editor.hide();
      this.resetEditor();
      this.content.show();
    },

    done: function() {
      this.editor.hide();
      this.updateContent();
      this.content.show();
    },

    destroy: function() {
      this.model.destroy();
      // app.func.setDirty();
    }
  });
});
