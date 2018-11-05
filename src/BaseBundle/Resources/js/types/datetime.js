$(function () {
  $('.p-form-datepicker').each(function() {
    var input = $(this).find('input');
    var id = '#'+$(this).attr('id');
    Perform.base.form.datepicker(id, {
      inputName: input.attr('name'),
      initialValue: input.val(),
      flatPickrConfig: $(this).data('flat-picker-config'),
      disabled: !!input.attr('disabled'),
    });
  });
});
