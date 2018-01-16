$(function() {
  $('.perform-media-type .btn').click(function(e) {
    e.preventDefault();

    var container = $(this).parents('.perform-media-type');
    var input = container.find('input');
    var indicator = container.find('.filename');

    Perform.media.selectFile({
      onSelect: function(files) {
        input.val(files[0].id);
        indicator.text(files[0].name);
      }
    });
  });

  $('.p-media-preview').each(function(e) {
    Perform.media.preview(this, $(this).data());
  });
});
