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

  function responsiveView() {
    var wSize = $(window).width();
    if (wSize <= 768) {
      $('#container').addClass('sidebar-close');
      $('#sidebar > ul').hide();
    }

    if (wSize > 768) {
      $('#container').removeClass('sidebar-close');
      $('#sidebar > ul').show();
    }
  }
  $(window).on('load', responsiveView);
  $(window).on('resize', responsiveView);

  $('#menu-toggle').click(function () {
    $(this).removeClass('fa-plus-square-o fa-minus-square-o');
    if ($('#sidebar > ul').is(":visible") === true) {
      $('#main-content').css({
        'margin-left': '0px'
      });
      $('#sidebar').css({
        'margin-left': '-210px'
      });
      $('#sidebar > ul').hide();
      $("#container").addClass("sidebar-closed");
      $(this).addClass('fa-plus-square-o');
    } else {
      $('#main-content').css({
        'margin-left': '210px'
      });
      $('#sidebar > ul').show();
      $('#sidebar').css({
        'margin-left': '0'
      });
      $("#container").removeClass("sidebar-closed");
      $(this).addClass('fa-minus-square-o');
    }
  });

  $('.wrapper form').each(function() {
    window.app.func.fancyForm($(this));
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
