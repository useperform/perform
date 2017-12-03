$(function() {
  $('.perform-media-type .btn').click(function(e) {
    var input = $(this).parents('.perform-media-type').find('input');
    e.preventDefault();
    Perform.media.selectFile({
      onSelect: function(file) {
        input.val(file.name);
      }
    });
  });
});
