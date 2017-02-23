$(function () {
  var bindButtons = function(selector) {
    $(selector).on('click', '.remove', function(e) {
      e.preventDefault();
      $(this).parent().remove();
    });
  }

  $('.add-track').click(function(e) {
    const index = $('.collection-row').length;
    const html = $('#template-track').html();
    const template = _.template(html.replace(/__name__/g, index));
    $('.collection-rows').append(template());
    e.preventDefault();
    bindButtons('.collection-row');
  });

  bindButtons('.collection-row');
});
