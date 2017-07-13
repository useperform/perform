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
