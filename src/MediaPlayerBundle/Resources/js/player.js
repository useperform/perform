import React from 'react';
import ReactDOM from 'react-dom';
import { onCommand } from './lib';
import { Item } from './components/Item';
import 'whatwg-fetch';

onCommand('play', data => {
  startPlaylist(data.playlist_id);
});

const startPlaylist = function(playlist_id) {
  const nowPlaying = document.getElementsByClassName('now-playing')[0];

  fetch(`/player/playlist/${playlist_id}`)
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

document.addEventListener('DOMContentLoaded', () => startPlaylist('initial'));
