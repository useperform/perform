import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';

Vue.use(Vuex);

export default new Vuex.Store({
  state: {
    files: [],
    page: 1,
  },

  getters: {
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
    changePage(state, page) {
      state.page = page;
    }
  },

  actions: {
    find(context) {
      axios.get('/admin/media/find?page='+context.state.page)
        .then(r => r.data)
        .then(function(data) {
          context.commit('loaded', data);
        });
    },

    nextPage(context) {
      context.commit('changePage', context.state.page + 1);
      context.dispatch('find');
    },

    prevPage(context) {
      if (context.state.page === 1) {
        return;
      }
      context.commit('changePage', context.state.page - 1);
      context.dispatch('find');
    },

    delete({commit}, id) {
      return axios.post("/admin/media/delete/"+id).then(function(response) {
          commit('remove', id);
      });
    }
  }
});
