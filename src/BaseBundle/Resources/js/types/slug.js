$(function () {
  $('.perform-type-slug').each(function() {
    var slugInput = $(this).find('input');
    var changed = false;

    slugInput.on('change', function(e) {
      changed = true;
    });

    $($(this).data('target')).on('keyup', function() {
      // slugify the target's value if the slug input is blank or hasn't changed
      // also slugify if it was changed to blank
      if (slugInput.val().trim() === '') {
        changed = false;
      }
      if (!changed) {
        slugInput.val(slugify($(this).val()));
      }
    });
  });

  var slugify = function(val) {
    return val.trim()
      .replace(/[^a-zA-Z0-9]+/g, '-')
      .replace(/-$/, '')
      .toLowerCase();
  };
});
