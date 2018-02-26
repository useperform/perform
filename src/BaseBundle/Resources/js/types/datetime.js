$(function () {
  $('.p-form-datepicker').each(function() {
    var input = $(this).find('input');
    var id = '#'+$(this).attr('id');
    Perform.base.form.datepicker(id, {
      inputName: input.attr('name'),
      initialValue: input.val(),
      format: $(this).data('format'),
      pickDate: $(this).data('pick-date'),
      pickTime: $(this).data('pick-time'),
      weekStart: $(this).data('week-start')
    });
  });
});
