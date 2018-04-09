import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    versionId: null,
    // richContent editor indexes, page names as keys
    // {
    //   main: 0,
    //   sidebar: 1,
    //   footer: 2,
    // }
    sections: {},
  },

  mutations: {
    setVersionId(state, payload) {
      state.versionId = payload.versionId;
    },
    addSection(state, payload) {
      const {name, editorIndex} = payload;
      if (!name || editorIndex == undefined) {
        console.error('Invalid section name or editorIndex');
      }
      state.sections[name] = editorIndex;
    },
  },

  actions: {
    loadVersion(context, data) {
      const {versionId} = data;
      // commit loading
      const url = '/admin/_page_editor/load/' + versionId;
      axios.get(url)
        .then(json => {
          context.commit('setVersionId', {
            versionId,
          });
          json.data.sections.forEach(section => {
            const editorIndex = context.state.sections[section.name];
            window.Perform.richContent.setContent(editorIndex, section.content);
          });
          // commit loaded
        });
    },
  }
});
