import Vue from 'vue';
import FilePreview from './components/FilePreview';

export default function preview(element, file) {
  //if file is not an object, fetch via ajax
  if (!file.id) {
    return;
  }
  new Vue({
    el: element,
    data: {
      file
    },
    components: {
      FilePreview,
    },
    template: '<FilePreview :file="file" />'
  });
};
