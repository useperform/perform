(function() {
  $('.p-html-editor').each(function() {
    var quill = new Quill(this, {
      theme: 'snow'
    });

    var form = $(this).parents('form');
    var input = $(this).parent().children('input[type=hidden]');
    form.on('submit', function() {
      input.val(quill.root.innerHTML);
    });
  });
})();
