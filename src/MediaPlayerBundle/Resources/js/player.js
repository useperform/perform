import React from 'react';
import ReactDOM from 'react-dom';
import { queueItem, onQueue } from './local-storage';
import { Item } from './Item';
import 'whatwg-fetch';

onQueue(event => {
  showItem(event.newValue);
});

const showItem = function(item) {
  const nowPlaying = document.getElementsByClassName('now-playing')[0];

  fetch(`/player/playlist/${item}`)
    .then(response => {
      return response.json()
    }).then(json => {
      ReactDOM.render(
          <Item json={json} />,
        nowPlaying
      );
    }).catch(e => {
      nowPlaying.innerHTML = '<p>No playlist</p>';
    });
};

document.addEventListener('DOMContentLoaded', () => showItem('initial'));
