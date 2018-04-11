$(function () {
  $('.perform-rich-content-type').each(function() {
    const editor = $(this).children('.form-group')
          .children('.editor')[0];
    const hiddenInput = $(this).children('input.content-id');

    window.Perform.richContent.init(editor, {
      contentId: hiddenInput.val(),
      showToolbar: true,
      onChange: function(store, editorIndex) {
        hiddenInput.val(store.state.editors[editorIndex].contentId);
      },
    });
  });
});
