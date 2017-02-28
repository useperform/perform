$(function () {
  var bindButtons = function(selector) {
    $(selector).on('click', '.remove-item', function(e) {
      e.preventDefault();
      $(this).parent().parent().remove();
    });
  }

  $('.collection .add-item').click(function(e) {
    e.preventDefault();
    var parent = $(this).parent();
    var index = parent.find('.collection-row').length;
    var field = parent.data('field');
    var html = $('#template-collection-'+field).html();
    var template = _.template(html.replace(/__name__/g, index));
    $('#collection-'+field).append(template());
    bindButtons('.collection-row');
  });

  bindButtons('.collection-row');
});
