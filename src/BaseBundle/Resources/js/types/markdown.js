$(function () {
  $('.p-form-markdown-preview').each(function() {
    var input = $(this).parents('.row').find('textarea');
    Perform.base.form.markdown(input, $(this));
  });
});
