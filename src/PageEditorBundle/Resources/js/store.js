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
            Perform.richContent.store.commit('CONTENT_SET_DATA', {
              editorIndex,
              data: section.content,
            });
            Perform.richContent.store.commit('CONTENT_SET_ID', {
              editorIndex,
              contentId: section.content.id
            });
          });
          // commit loaded
        });
    },

    save(context) {
      // commit saving
      const url = '/admin/_page_editor/save/' + context.state.versionId;
      const data = Perform.richContent.store.getters.allSaveOperations;
      axios.post(url, data)
        .then(json => {
          json.data.updates.forEach(update => {
            const editorIndex = Perform.richContent.store.getters.editorIndexesWithContentId(update.contentId)[0];
            Perform.richContent.store.commit('CONTENT_HANDLE_NEW_BLOCKS', {
              editorIndex,
              newBlockIds: update.newBlockIds,
            });
          });
        });
    }
  }
});
