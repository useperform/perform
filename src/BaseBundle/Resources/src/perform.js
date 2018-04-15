import jquery from 'jquery';

window.jQuery = window.$ = jquery;

let perform = {};

import base from 'perform-base/module';

perform.base = base;

window.Perform = perform;

import {renderDropdown, renderCounter, getUnfinished} from 'perform-base/tasks';

$(function () {
  renderDropdown('#perform-tasks-dropdown');
  renderCounter('#perform-tasks-counter');

  window.addEventListener('beforeunload', function(e) {
    if (getUnfinished().length > 0) {
      var confirmationMessage = "You have unfinished tasks. Leave this page?";
      e.returnValue = confirmationMessage;
      return confirmationMessage;
    }
  });

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

  $('.sidebar-toggler').click(function () {
    var wrapper = $('.wrapper');
    var sidebar = $('#sidebar');
    if (wrapper.hasClass('sidebar-closed')) {
      sidebar.show();
      wrapper.removeClass('sidebar-closed');
    } else {
      sidebar.hide();
      wrapper.addClass('sidebar-closed');
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
