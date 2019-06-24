$(function () {
  $('#nav-accordion').dcAccordion({
    eventType: 'click',
    autoClose: false,
    saveState: true,
    disableLink: true,
    speed: 'fast',
    showCount: false,
    autoExpand: true,
    cookie: 'menu-state',
    classExpand: 'dcjq-current-parent'
  });

  var sidebar = $('#sidebar');
  if (sidebar.is(':visible')) {
    sidebar.addClass('open');
  } else {
    sidebar.addClass('closed');
  }
  var workspace = $('#workspace');

  $('.sidebar-toggler').click(function () {
    if (sidebar.hasClass('open')) {
      sidebar
        .addClass('closed')
        .removeClass('open');
      workspace.addClass('sidebar-closed');
    } else {
      sidebar
        .addClass('open')
        .removeClass('closed');
      workspace.removeClass('sidebar-closed');
    }
  });

  $('.wrapper form').each(function() {
    Perform.base.fancyForm($(this));
  });

  $('.tooltips').tooltip();

  $('.table-crud tbody tr td').click(function(e) {
    if (e.target === this) {
      var checkbox = $(this).parent().find('.selector');
      checkbox.prop('checked', !checkbox.prop('checked'));
    }
  });

  $('.table-crud thead .batch-selector').change(function(e) {
    $('.table-crud tbody input[type=checkbox].selector').prop('checked', $(this).prop('checked'));
  });
});
