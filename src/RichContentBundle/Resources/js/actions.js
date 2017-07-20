import 'whatwg-fetch';
import blockTypes from './components/blocktypes';

export function loadContent(editorIndex, id) {
  return function(dispatch) {
    const url = '/admin/_editor/content/get/' + id;
    fetch(url, {
      credentials: 'include',
    }).then(res => {
      return res.json();
    }).then(json => {
      dispatch({
        type: 'CONTENT_LOAD',
        editorIndex: editorIndex,
        id: id,
        status: true,
        json,
      });
    });
  }
}

const handleFetch = function(response) {
  if (!response.ok) {
    throw Error('An error occurred saving this content. Please try again.');
  }

  return response.json();
}

export function getPostBody(state, editorIndex) {
  let currentBlocks = {};
  let newBlocks = {}
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
}

export function save(editorIndex, onSuccess, onError) {
  return function (dispatch, getState) {
    dispatch({
      type: 'CONTENT_SAVE',
      editorIndex: editorIndex,
      status: false
    });
    const contentId = getState().editors[editorIndex].contentId;
    const url = contentId ? '/admin/_editor/content/save/' + contentId
          : '/admin/_editor/content/save-new';

    fetch(url, {
      body: JSON.stringify(getPostBody(getState(), editorIndex)),
      credentials: 'include',
      method: 'POST'
    }).then(handleFetch)
      .then(json => {
        dispatch({
          type: 'CONTENT_SAVE',
          editorIndex: editorIndex,
          status: true,
          json: json
        });
        onSuccess(json);
      })
      .catch(error => {
        app.func.showError(error);
        onError(error);
      });
  }
}

export function addBlock(type, editorIndex) {
  return {
    type: 'BLOCK_ADD',
    blockType: type,
    editorIndex,
    value: blockTypes[type].defaults
  }
}

export function removeBlock(position) {
  return {
    type: 'BLOCK_REMOVE',
    position
  }
}

export function moveBlock(currentPosition, newPosition) {
  return {
    type: 'BLOCK_MOVE',
    currentPosition,
    newPosition,
  }
}
