import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import blockTypes from './components/blocktypes';

Vue.use(Vuex);

// convert a editor state into a request body for the save endpoint
const getPostBody = function(state, editorIndex) {
  let currentBlocks = {};
  let newBlocks = {};
  let order = [];
  const blockIds = Object.keys(state.blocks);

  state.editors[editorIndex].order.forEach(item => {
    let id = item[0];
    let block = state.blocks[id];
    order.push(item[0]);

    // new blocks have a stub id starting with _
    if (id.substring(0, 1) === '_') {
      newBlocks[id] = block;
      return;
    }
    currentBlocks[id] = block;
  });

  return {
    blocks: currentBlocks,
    newBlocks,
    order,
  };
};


const newId = function() {
  return Math.random().toString().substring(2);
}

const editorsUseBlock = function(editors, blockId) {
  return editors.some(editor => {
    return editor.order.some(order => {
      return order[0] === blockId;
    });
  });
}

export default new Vuex.Store({
  state: {
    // Block entities indexed by database id and shared across all editors.
    // Blocks that have not been saved to the database are indexed by a random id beginning with an underscore.
    // When these blocks are saved, their keys are updated.
    // new
    // {
    //   '_000000': {value: 'new block'},
    //   '12345': {value: 'exisiting block'},
    // }
    // saved
    // {
    //   '12346': {value: 'new block'},
    //   '12345': {value: 'exisiting block'},
    // }
    blocks: {},
    editors: [],
  },

  getters: {
  },

  mutations: {
    EDITOR_ADD (state, payload) {
      state.editors.push({
        contentId: payload.contentId,
        loaded: false,
        order: [],
      });
    },

    CONTENT_LOAD(state, payload) {
      const {contentId, editorIndex, status, data} = payload;
      if (!contentId) {
        return;
      }

      if (!status) {
        //show loading state
        return;
      }

      // Associate each ordered block with a random id.
      // This will be used for the key on the block component to keep
      // track of DOM nodes, since we can't use the position in the
      // order array.
      const order = data.order.map(id => {
        return [id, newId()];
      });
      const editors = state.editors || [];
      editors[editorIndex] = Object.assign(editors[editorIndex] || {}, {
        order: order,
        contentId,
        loaded: true
      });

      state.blocks = Object.assign({}, state.blocks, data.blocks);
      state.editors = editors;
    },

    CONTENT_SAVE: function(state, payload) {
      const {json, editorIndex, status} = payload;

      if (!status) {
        //show loading state
        return;
      }

      // replace stub ids in blocks with new database ids
      let blocks = Object.assign({}, state.blocks);
      const newIds = Object.entries(json.newBlocks);
      for (let i=0; i < newIds.length; i++) {
        const stubId = newIds[i][0];
        const dbId = newIds[i][1];
        blocks[dbId] = state.blocks[stubId];
        delete blocks[stubId];
      }
      // replace stub ids in the order with new database ids
      const editors = state.editors.map(editor => Object.assign({}, editor));
      const order = editors[editorIndex].order.map(item => {
        const dbId = json.newBlocks[item[0]];
        if (dbId) {
          return [dbId, item[1]];
        }

        return item
      });
      editors[editorIndex].order = order;
      editors[editorIndex].contentId = json.id;

      state.blocks = blocks;
      state.editors = editors;
    },

    BLOCK_ADD(state, payload) {
      const {blockType, editorIndex} = payload;
      // set an arbitrary unique id, since there is no database id for
      // this new block
      const id = '_'+newId();
      let blocks = Object.assign({}, state.blocks);
      blocks[id] = {
        id,
        type: blockType,
        value: blockTypes[blockType].defaults,
      };
      state.blocks = blocks;

      state.editors[editorIndex].order = [
        ...state.editors[editorIndex].order,
        [id, newId()],
      ];
    },

    BLOCK_UPDATE: function(state, payload) {
      console.log(state, payload);
      state.blocks[payload.id].value = payload.value;
    },
  },

  actions: {
    loadContent({commit}, data) {
      const {editorIndex, contentId} = data;
      const url = '/admin/_editor/content/get/' + contentId;
      axios.get(url)
        .then(json => {
          commit('CONTENT_LOAD', {
            editorIndex,
            contentId,
            status: true,
            data: json.data,
          });
        });
    },

    save(context, payload) {
      const {editorIndex, onSuccess, onError} = payload;
      context.commit('CONTENT_SAVE', {
        editorIndex,
        status: false
      });
      const contentId = context.state.editors[editorIndex].contentId;
      const url = contentId ? '/admin/_editor/content/save/' + contentId
              : '/admin/_editor/content/save-new';

      axios.post(url, getPostBody(context.state, editorIndex))
        .then(json => {
          context.commit('CONTENT_SAVE', {
            editorIndex: editorIndex,
            status: true,
            json: json.data
          });
          onSuccess(json);
        })
        .catch(error => {
          Perform.base.showError(error);
          onError(error);
        });
    },

    removeBlock(position) {
      return {
        type: 'BLOCK_REMOVE',
        position
      }
    },

    moveBlock(currentPosition, newPosition) {
      return {
        type: 'BLOCK_MOVE',
        currentPosition,
        newPosition,
      }
    }
  },
});
