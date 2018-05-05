$(function() {
  $('.perform-media-type .btn').click(function(e) {
    e.preventDefault();

    var container = $(this).parents('.perform-media-type');
    var input = container.find('input');
    var indicator = container.find('.filename');
    var preview = container.find('.p-comp-media-preview');

    Perform.media.selectFile({
      onSelect: function(files) {
        input.val(files[0].id);
        indicator.text(files[0].name);
        Perform.media.preview(preview[0], files[0]);
      }
    });
  });
});
