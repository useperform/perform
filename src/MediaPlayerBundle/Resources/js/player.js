import 'whatwg-fetch';
import Player from './components/Player';
import ReactDOM from 'react-dom';
import React from 'react';
import { Item } from './components/Item';

const startPlaylist = function(playlist_id) {
  const nowPlaying = document.getElementById('now-playing');

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

document.addEventListener('DOMContentLoaded', () => {
  const player = document.getElementById('player');

  ReactDOM.render(
      <Player />,
    player
  );
});
