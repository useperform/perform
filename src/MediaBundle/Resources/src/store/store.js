import Vue from 'vue'
import Vuex from 'vuex'
import axios from 'axios'

Vue.use(Vuex)

export default new Vuex.Store({
  state: {
    files: [],
  },

  mutations: {
    loaded (state, files) {
      state.files = files;
    },
    remove (state, id) {
      state.files = state.files.filter(file => {
        return file.id !== id;
      });
    },
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
  }
});
