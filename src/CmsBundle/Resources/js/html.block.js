$(function () {
  app.models.HtmlBlock = app.models.ContentBlock.extend({
    defaults: function() {
      return {
        type: 'html',
        value: {
          content: '<p>Your content here</p>'
        },
      };
    }
  });

  app.views.HtmlBlockView = app.views.BlockView.extend({
    name: 'html',
  });

  app.views.HtmlEditorView = app.views.EditorView.extend({
    name: 'html',

    updateModel: function() {
      var value = {
        content: this.$el.find('textarea').val()
      };
      this.model.set('value', value);
    },
  });
});
