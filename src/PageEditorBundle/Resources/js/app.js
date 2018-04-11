import Toolbar from './components/Toolbar';
import Vue from 'vue';
import store from './store';

const editPage = function(versionId) {
  const parent = document.createElement('div');
  document.body.appendChild(parent);

  new Vue({
    el: parent,
    render: h => h(Toolbar, {props: {finishUrl: '/admin/_page_editor/end'}}),
    store,
  });

  var sections = document.getElementsByClassName('perform-page-editor-section');
  // section elements are removed from the htmlcollection as rich
  // content editors are added, so iterate in reverse to avoid
  // disrupting indexes
  for (var i=sections.length; i--;) {
    var section = sections[i];
    var editorIndex = Perform.richContent.init(section, {
      noLoad: true,
    });
    store.commit('addSection', {
      name: section.getAttribute('data-section-name'),
      editorIndex,
    });
  }

  store.dispatch('loadVersion', {
    versionId,
  });
};

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.pageEditor = {
  editPage,
};
