import Vue from 'vue';
import FilePreview from './components/FilePreview';

export default function(el, file) {
  //if file is not an object, fetch via ajax
  if (!file.id) {
    return;
  }
  new Vue({
    el,
    render: h => h(FilePreview, {props: {
      file
    }}),
  });
};
