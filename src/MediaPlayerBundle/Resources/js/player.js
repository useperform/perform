import 'whatwg-fetch';
import Player from './components/Player';
import ReactDOM from 'react-dom';
import React from 'react';
import { Item } from './components/Item';

document.addEventListener('DOMContentLoaded', () => {
  const player = document.getElementById('player');

  ReactDOM.render(
      <Player />,
    player
  );
});
