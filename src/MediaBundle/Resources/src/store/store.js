import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    files: [],
  },

  getters: {
    selectedFiles(state) {
      return state.files.filter(file => {
        return file.selected;
      });
    }
  },

  mutations: {
    loaded (state, files) {
      state.files = files.map((file) => {
        return Object.assign({}, file, {
          selected: false,
        });
      });
    },
    remove (state, id) {
      state.files = state.files.filter(file => {
        return file.id !== id;
      });
    },
    toggleSelect (state, id) {
      for (var i=0; i < state.files.length; i++) {
        if (state.files[i].id !== id) {
          continue;
        }
        state.files[i].selected = !state.files[i].selected;
        return;
      }
    }
  },

  actions: {
    find({commit}) {
      axios.get('/admin/media/find')
        .then(r => r.data)
        .then(function(data) {
          commit('loaded', data);
        });
    },

    delete({commit}, id) {
      return axios.post("/admin/media/delete/"+id).then(function(response) {
          commit('remove', id);
      });
    },

    toggleSelect({commit}, id) {
      commit('toggleSelect', id);
    }
  }
});
