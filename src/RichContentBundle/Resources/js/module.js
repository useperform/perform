import Vue from 'vue';
import edit from './edit';

export default {
  edit,

  form: {
    richContent(options) {
      const hiddenInput = document.querySelector(options.input);
      edit(options.el, {
        contentId: hiddenInput.value,
        showToolbar: true,
        onChange(store, editorIndex) {
          hiddenInput.value = store.state.editors[editorIndex].contentId;
        }
      });
    }
  },
};
