import Toolbar from './components/Toolbar';
import Vue from 'vue';

const editPage = function() {
  const parent = document.createElement('div');
  document.body.appendChild(parent);

  new Vue({
    el: parent,
    render: h => h(Toolbar)
  });

  var sections = document.getElementsByClassName('perform-page-editor-section');
  // section elements are removed from the htmlcollection as rich
  // content editors are added, so iterate in reverse to avoid
  // disrupting indexes
  for (var i=sections.length; i--;) {
    var section = sections[i];
    Perform.richContent.init(section, {
      contentId: section.getAttribute('data-content-id'),
    });
  }
};

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.pageEditor = {
  editPage,
};
