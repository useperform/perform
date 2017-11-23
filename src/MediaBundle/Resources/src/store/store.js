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

    upload(context, file) {
      const formData = new FormData();
      formData.append("file", file);
      return new Promise((resolve, reject) => {
        axios.post("/admin/media/upload", formData).then(function(response) {
          resolve(response);
        });
      });
    },

    delete({commit}, id) {
      return new Promise((resolve, reject) => {
        axios.post("/admin/media/delete/"+id).then(function(response) {
          commit('remove', id);
          resolve(response);
        });
      });
    },
  }
});
