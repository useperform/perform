$(function () {
  var bindButtons = function(collection) {
    $(collection).on('click', '.link-actions .remove-item', function(e) {
      e.preventDefault();
      var rows = $(this).parents('.collection-rows');
      $(this).parents('.collection-row').remove();
      reorderRows(rows);
    });

    $(collection).on('click', '.link-actions .move-up', function(e) {
      e.preventDefault();
      var row = $(this).parents('.collection-row');
      var target = row.prev();
      row.insertBefore(target);
      reorderRows($(this).parents('.collection-rows'));
    });

    $(collection).on('click', '.link-actions .move-down', function(e) {
      e.preventDefault();
      var row = $(this).parents('.collection-row');
      var target = row.next();
      row.insertAfter(target);
      reorderRows($(this).parents('.collection-rows'));
    });
  }

  var reorderRows = function(rows) {
    rows.find('.collection-row').each(function(index) {
      var inputId =
            $(this).data('form-id') + '_' +
            $(this).parents('.collection').data('sort-field');
      $(this).find('#'+inputId).val(index);
    });
  }

  $('.collection .add-item').click(function(e) {
    e.preventDefault();

    var collection = $(this).parents('.collection');
    var field = collection.data('field');
    var html = $('#template-collection-'+field).html();
    var index = collection.data('collection-index') ? collection.data('collection-index') : collection.find('.collection-row').length;
    var template = _.template(html.replace(/__name__/g, index));

    collection.children('.collection-rows').append(template());
    window.app.func.fancyForm($('.wrapper form'));
    collection.data('collection-index', index + 1);
    bindButtons(collection);
    reorderRows(collection);
  });

  bindButtons($('.collection-row'));
  reorderRows($('.collection-rows'));
});
