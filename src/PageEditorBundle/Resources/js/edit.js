import Toolbar from './components/Toolbar';
import Vue from 'vue';
import store from './store';
import edit from 'perform-rich-content/js/edit';

export default function(versionId) {
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
    var editorIndex = edit(section, {
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
