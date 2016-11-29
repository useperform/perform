$(function () {
  $('.collection-row .remove').click(function(e) {
    e.preventDefault();
    $(this).parent().remove();
  });
});
