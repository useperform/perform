import React from 'react';
import ReactDOM from 'react-dom';
import { queueItem, onQueue } from './local-storage';
import { Item } from './Item';
import 'whatwg-fetch';

onQueue(event => {
  showItem(event.newValue);
});

const showItem = function(item) {
  fetch(`/player/playlist/${item}`)
    .then(response => {
      return response.json()
    }).then(json => {
      ReactDOM.render(
          <Item json={json} />,
        document.getElementById('app')
      );
    }).catch(e => {
      document.getElementById('app').innerHTML = '<p>No playlist</p>';
    });
};

document.addEventListener('DOMContentLoaded', () => showItem('initial'));
