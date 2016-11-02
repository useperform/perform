import React from 'react';
import ReactDOM from 'react-dom';
import { onCommand } from './lib';
import { Item } from './components/Item';
import 'whatwg-fetch';
import Audio from 'react-howler';

onCommand('play', data => {
  play(data.url);
});

const play = function(url) {
  const nowPlaying = document.getElementsByClassName('now-playing')[0];
  console.log(url);

  ReactDOM.render(
    <div>
      <p>{url}</p>
      <Audio src={url} playing={true} />
    </div>,
    nowPlaying
  );
};

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
