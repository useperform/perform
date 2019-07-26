import $ from 'jquery';
import './lib/dcjqaccordian';
import 'jquery.cookie';
import 'expose-loader?Popper!popper.js/dist/umd/popper.js'
import 'bootstrap/js/dist/tooltip';
import 'bootstrap/js/dist/dropdown';
import 'select2';

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
  $(this).find('.select2').select2();
});

$('.tooltips').tooltip();
