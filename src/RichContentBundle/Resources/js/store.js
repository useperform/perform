import Vue from 'vue';
import Vuex from 'vuex';
import axios from 'axios';
import blockTypes from './components/blocktypes';

Vue.use(Vuex);

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
    // convert an editor state into a request body for saving
    editorSaveOperation: (state) => (editorIndex) => {
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
        contentId: state.editors[editorIndex].contentId,
        blocks: currentBlocks,
        newBlocks,
        order,
      };
    },

    allSaveOperations(state, getters) {
      let operations = [];
      for (let i=0; i < state.editors.length; i++) {
        operations.push(getters.editorSaveOperation(i));
      }

      return operations;
    },

    editorIndexesWithContentId: (state) => (contentId) => {
      let indexes = [];
      for (let i=0; i < state.editors.length; i++) {
        if (state.editors[i].contentId === contentId) {
          indexes.push(i);
        }
      }

      return indexes;
    }
  },

  mutations: {
    EDITOR_ADD(state, payload) {
      state.editors.push({
        contentId: payload.contentId,
        loading: false,
        saving: false,
        loaded: false,
        order: [],
      });
    },

    CONTENT_LOADING(state, payload) {
      const {editorIndex} = payload;
      state.editors[editorIndex].loading = true;
    },

    CONTENT_LOADED(state, payload) {
      const {editorIndex} = payload;
      state.editors[editorIndex].loading = false;
    },

    CONTENT_SET_ID(state, payload) {
      const {editorIndex, contentId} = payload;
      state.editors[editorIndex].contentId = contentId;
    },

    CONTENT_SET_DATA(state, payload) {
      const {editorIndex, data} = payload;

      // Associate each ordered block with a random id.
      // This will be used for the key on the block component to keep
      // track of DOM nodes, since we can't use the position in the
      // order array.
      const order = data.blockOrder.map(id => {
        return [id, newId()];
      });
      state.editors[editorIndex].order = order;
      state.editors[editorIndex].loaded = true;

      const blocks = {};
      data.blocks.forEach(block => {
        blocks[block.id] = block;
      });
      state.blocks = Object.assign({}, state.blocks, blocks);
    },

    CONTENT_SAVING: function(state, payload) {
      const {editorIndex} = payload;
      state.editors[editorIndex].saving = true;
    },

    CONTENT_SAVED(state, payload) {
      const {editorIndex} = payload;
      state.editors[editorIndex].saving = false;
    },

    CONTENT_HANDLE_NEW_BLOCKS: function(state, payload) {
      const {newBlockIds, editorIndex} = payload;

      // replace stub ids in blocks with new database ids
      let blocks = Object.assign({}, state.blocks);
      const newIds = Object.entries(newBlockIds);
      for (let i=0; i < newIds.length; i++) {
        const stubId = newIds[i][0];
        const dbId = newIds[i][1];
        blocks[dbId] = state.blocks[stubId];
        delete blocks[stubId];
      }
      // replace stub ids in the order with new database ids
      const editors = state.editors.map(editor => Object.assign({}, editor));
      const order = editors[editorIndex].order.map(item => {
        const dbId = newBlockIds[item[0]];
        if (dbId) {
          return [dbId, item[1]];
        }

        return item;
      });
      editors[editorIndex].order = order;

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
        component_info: {},
      };
      state.blocks = blocks;

      state.editors[editorIndex].order = [
        ...state.editors[editorIndex].order,
        [id, newId()],
      ];
    },

    BLOCK_UPDATE: function(state, payload) {
      state.blocks[payload.id].value = payload.value;
      if (payload.info) {
        state.blocks[payload.id].component_info = payload.info;
      }
    },

    BLOCK_MOVE: function(state, payload) {
      const pos = payload.from;
      const newPos = payload.to;
      const newIndex = newPos[1] < 0 ? 0 : newPos[1];

      let blocks = Object.assign({}, state.blocks);
      let editors = state.editors.map(editor => Object.assign({}, editor));
      let sourceOrder = editors[pos[0]].order.slice();
      let destOrder = newPos[0] === pos[0] ? sourceOrder : editors[newPos[0]].order.slice();
      // blockRef is an array of [block-id, key-id]
      let blockRef = sourceOrder[pos[1]];
      if (!blockRef) {
        return;
      }
      const blockId = blockRef[0];
      const block = blocks[blockId];

      sourceOrder.splice(pos[1], 1);
      editors[pos[0]].order = sourceOrder;

      // if the block is not used anywhere else, give it a new id, marking it as new.
      // saving the source editor before the destination editor will
      // delete the block from the database, so it needs to be marked as
      // new to allow saving it again.
      if (pos[0] !== newPos[0] && !editorsUseBlock(editors, blockId)) {
        delete blocks[blockId];
        blockRef = ['_'+newId(), blockRef[1]];
        blocks[blockRef[0]] = block;
      }

      destOrder.splice(newIndex, 0, blockRef);
      editors[newPos[0]].order = destOrder;

      state.blocks = blocks;
      state.editors = editors;
    },

    BLOCK_REMOVE: function(state, payload) {
      const {position, editorIndex} = payload;
      const blockId = state.editors[editorIndex].order[position][0];

      let order = state.editors[editorIndex].order;
      state.editors[editorIndex].order = [
        ...order.slice(0, position),
        ...order.slice(position + 1),
      ];

      if (!editorsUseBlock(state.editors, blockId)) {
        delete state.blocks[blockId];
      }
    },
  },

  actions: {
    loadContent({commit}, data) {
      const {editorIndex, contentId} = data;
      commit('CONTENT_LOADING', {
        editorIndex
      });
      const url = '/admin/_editor/content/get/' + contentId;
      axios.get(url)
        .then(json => {
          commit('CONTENT_SET_DATA', {
            editorIndex,
            contentId,
            data: json.data,
          });
          commit('CONTENT_LOADED', {
            editorIndex
          });
        });
    },

    save(context, payload) {
      const {editorIndex, onSuccess, onError} = payload;
      context.commit('CONTENT_SAVING', {editorIndex});
      const url = '/admin/_editor/content/save';
      axios.post(url, context.getters.editorSaveOperation(editorIndex))
        .then(json => {
          context.commit('CONTENT_HANDLE_NEW_BLOCKS', {
            editorIndex,
            newBlockIds: json.data.newBlockIds
          });
          context.commit('CONTENT_SET_ID', {
            editorIndex,
            contentId: json.data.contentId
          });
          context.commit('CONTENT_SAVED', {editorIndex});
          onSuccess(json);
        })
        .catch(error => {
          Perform.base.showError('An error occurred.');
          console.error(error);
          onError(error);
        });
    },
  },
});
