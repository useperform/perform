$(function () {

  var Perform = window.Perform;
  if (!Perform.media) {
    Perform.media = {};
  }

  Perform.media.UploadModel = Backbone.Model.extend({
    defaults : function() {
      return {
        filename: 'Upload',
        status: 'pending', //pending, done, error
        progress: 0
      };
    }
  });

  Perform.media.UploadView = Backbone.View.extend({
    tagName: "div",

    template: _.template($('#template-upload').html()),

    initialize: function() {
      this.model.on('change', this.render, this);
    },

    render: function() {
      this.$el.html(this.template(this.model.toJSON()));
      return this;
    }
  });

  var uploads = [];

  // Initialize the jQuery File Upload widget:
  $('.file-upload').fileupload({
    // Uncomment the following to send cross-domain cookies:
    //xhrFields: {withCredentials: true},
    // url: '/admin/media/upload.json',
    maxChunkSize: 2000000,
    dataType: 'json',
    add: function (e, data) {
      for( var i=0; i < data.files.length; i++){
        var name = data.files[i].name;
        var upload = new Perform.media.UploadModel({filename: name});
        var view = new Perform.media.UploadView({model: upload});
        uploads[name] = upload;
        $('.file-list').append(view.render().el);
      }
      data.submit();
    },

    done: function (e, data) {
      for( var i=0; i < data.files.length; i++){
        var name = data.files[i].name;
        uploads[name].set('status', 'done');
      }
    },

    progress: function (e, data) {
      for( var i=0; i < data.files.length; i++){
        var name = data.files[i].name;
        var progress = parseInt(data.loaded / data.total * 100, 10);
        uploads[name].set('progress', progress);
      }
    },

    fail: function (e, data) {
      for( var i=0; i < data.files.length; i++){
        var name = data.files[i].name;
        uploads[name].set('status', 'error');
      }
    }
  });
});
