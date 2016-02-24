var $ = require('jquery');
require("./lib/dcjqaccordian.js")
require("../vendor/jquery.cookie/jquery.cookie.js")

$(function() {
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
});
