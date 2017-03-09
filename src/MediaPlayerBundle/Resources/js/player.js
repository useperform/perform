import Player from './components/Player';
import ReactDOM from 'react-dom';
import React from 'react';

document.addEventListener('DOMContentLoaded', () => {
  const element = document.getElementById('player');

  ReactDOM.render(
      <Player ref={(player) => player.startDefaultPlaylist()}/>,
    element
  );
});
