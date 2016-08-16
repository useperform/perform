$(function() {
  var app = window.app;

  app.models.ContentBlock = Backbone.Model.extend({
    initialize: function() {
      this.bind('change', app.func.setDirty);
    },

    viewClass: function() {
      var type = this.get('type');
      return type.charAt(0).toUpperCase() + type.slice(1) + 'BlockView';
    },

    editorClass: function() {
      var type = this.get('type');
      return type.charAt(0).toUpperCase() + type.slice(1) + 'EditorView';
    },

    createEditor: function() {
      return new app.views[this.editorClass()]({
        model: this
      });
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
    },

    initialize: function() {
      this.listenTo(this.model, 'change', this.render);
    },

    render: function() {
      this.$el.html(this.template(this.model.toJSON()));
      this.content = this.$('.content');
      return this;
    },

    edit: function() {
      app.func.editBlock(this.model);
    },

    destroy: function() {
      this.model.destroy();
      // app.func.setDirty();
      this.remove();
    }
  });

  app.views.EditorView = Backbone.View.extend({
    tagName: "div",

    template: function(json) {
      return _.template($('#content-editor-' + this.name).html())(json);
    },

    render: function() {
      this.$el.html(this.template(this.model.toJSON()));
      return this;
    },

    updateModel: function() {}
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
    },

    writableData: function() {
      var data = [];
      var blocks = _.filter(this.models, function(block) {
        return !block.get('readonly');
      });
      _.each(blocks, function(block) {
        data.push({
          type: block.get('type'),
          value: block.get('value')
        });
      });

      return data;
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

  app.views.AddBlockView = Backbone.View.extend({
    tagName: "div",

    render: function(blocks) {
      var template = _.template($('#template-add-block').html());
      this.$el.html(template(blocks));
      return this;
    },

    events: {
      "click a.add-block-type": "addBlock",
    },

    addBlock: function(e) {
      e.preventDefault();
      var type = $(e.target).data('type');
      if (!type) {
        console.error('.add-block-type link must have a type');
        return;
      }
      app.func.addBlock(type);
    },
  });
});
