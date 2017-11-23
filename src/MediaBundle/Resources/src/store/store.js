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

    delete(context, id) {
      console.log('deleting file');
      console.log(id);
    },
  }
});
