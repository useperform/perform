$(function () {
  app.models.TextBlock = app.models.ContentBlock.extend({
    defaults: function() {
      return {
        type: 'text',
        value: {
          content: 'Text content'
        },
      };
    }
  });

  app.views.TextBlockView = app.views.BlockView.extend({
    name: 'text',
  });

  app.views.TextEditorView = app.views.EditorView.extend({
    name: 'text',

    updateModel: function() {
      var value = {
        content: this.$el.find('textarea').val()
      };
      this.model.set('value', value);
    },
  });
});
