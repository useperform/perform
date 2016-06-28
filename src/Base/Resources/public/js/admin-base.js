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

  $('#modal-delete').on('show.bs.modal', function (e) {
    var link = $(e.relatedTarget);
    $(this).find('form').attr('action', link.data('action'));
  });

  var app = {
    models: {},
    views: {},
    collections: {},
    vars: {},
    func: {
      fancyForm: function(form) {
        // form.find('.select2').select2();
        form.find('.datepicker').each(function() {
          $(this).datetimepicker({
            format: $(this).data('format'),
            showTodayButton: true,
            showClear: true
          });
        });
      }
    }
  };

  $('.wrapper form').each(function() {
    app.func.fancyForm($(this));
  });

  $('.tooltips').tooltip();

  window.app = app;
});
