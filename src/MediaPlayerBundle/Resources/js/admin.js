$(function () {
  $(document).on('change', 'select', function() {
    var select = $(this);
    var title = select.parents('.collection-row').find('input[type=text]');
    var selectedLabel = select.find('option:selected').text();
    if (title.val() === '' || title.val() === $(this).data('selected-label')) {
      title.val(selectedLabel);
      $(this).data('selected-label', selectedLabel)
    }
  });
});
