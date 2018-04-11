import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    versionId: null,
    versionTitle: '',
    // array of available version objects
    versions: [],
    // richContent editor indexes, page names as keys
    // {
    //   main: 0,
    //   sidebar: 1,
    //   footer: 2,
    // }
    sections: {},
  },

  mutations: {
    setCurrentVersion(state, payload) {
      const {title, id} = payload;
      state.versionTitle = title;
      state.versionId = id;
    },
    setVersions(state, payload) {
      state.versions = payload.versions;
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
          context.commit('setCurrentVersion', {
            id: versionId,
            title: json.data.version.title
          });
          context.commit('setVersions', {
            versions: json.data.availableVersions,
          });

          Perform.richContent.store.commit('EDITORS_CLEAR', {
            editorIndexes: Object.values(context.state.sections),
          });
          // reset blocks in rich content to prevent memory leaks
          // Perform.richContent.store.commit('BLOCKS_CLEAR');

          json.data.version.sections.forEach(section => {
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
      const url = '/admin/_page_editor/save';
      let data = {
        versionId: context.state.versionId,
        sections: {},
      };
      Object.keys(context.state.sections).forEach(sectionName => {
        data.sections[sectionName] = Perform.richContent.store.getters.editorSaveOperation(context.state.sections[sectionName]);
      });
      axios.post(url, data)
        .then(json => {
          let updates = json.data.updates;
          Object.keys(updates).forEach(sectionName => {
            let editorIndex = context.state.sections[sectionName];
            let update = updates[sectionName];
            Perform.richContent.store.commit('CONTENT_SET_ID', {
              editorIndex,
              contentId: update.contentId,
            });
            Perform.richContent.store.commit('CONTENT_HANDLE_NEW_BLOCKS', {
              editorIndex,
              newBlockIds: update.newBlockIds,
            });
          });
          alert('Version saved.');
        });
    },

    publish(context) {
      const url = '/admin/_page_editor/publish/'+context.state.versionId;
      axios.post(url)
        .then(json => {
          alert('Version published.');
        });
    }
  }
});
