import 'whatwg-fetch';

export function loadContent(id) {
  return function(dispatch) {
    const url = '/admin/_editor/content/get/' + id;
    fetch(url, {
      credentials: 'include',
    }).then(res => {
      return res.json();
    }).then(json => {
      dispatch({
        type: 'CONTENT_LOAD',
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

const getPostBody = function(state) {
  return {
    newBlocks: state.newBlocks,
    blocks: state.blocks,
    order: state.order.map(i => {
      // an array with the block id and a unique react key
      // we only want the block id
      return i[0];
    })
  };
}

export function save(onSuccess, onError) {
  return function (dispatch, getState) {
    dispatch({
      type: 'CONTENT_SAVE',
      status: false
    });
    const contentId = getState().contentId;
    const url = contentId ? '/admin/_editor/content/save/' + contentId
          : '/admin/_editor/content/save-new';

    fetch(url, {
      body: JSON.stringify(getPostBody(getState())),
      credentials: 'include',
      method: 'POST'
    }).then(handleFetch)
      .then(json => {
        dispatch({
          type: 'CONTENT_SAVE',
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
