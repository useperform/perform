import Player from './components/Player';
import ReactDOM from 'react-dom';
import React from 'react';

document.addEventListener('DOMContentLoaded', () => {
  const player = document.getElementById('player');

  ReactDOM.render(
      <Player />,
    player
  );
});
