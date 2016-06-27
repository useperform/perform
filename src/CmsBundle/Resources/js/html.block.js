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

    resetEditor: function() {
      this.$el.find('.editor textarea').html(this.model.get('value').content);
    },

    updateContent: function() {
      var value = {
        content: this.$el.find('.editor textarea').val()
      };
      this.model.set('value', value);
    },
  });
});
