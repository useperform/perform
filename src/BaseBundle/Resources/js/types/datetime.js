$(function () {
  $('.p-form-datepicker').each(function() {
    var value = $(this).find('input').val();
    var id = '#'+$(this).attr('id');
    Perform.base.form.datepicker(id, {
      initialValue: value
    });
  });
});
