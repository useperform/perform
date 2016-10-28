import React from 'react';
import ReactDOM from 'react-dom';
import { queueItem, onQueue } from './local-storage';
import { Item } from './Item';

onQueue(event => {
  showItem(event.newValue);
});

const showItem = function(item) {
  ReactDOM.render(
      <Item item={item} />,
    document.getElementById('app')
  );
};

document.addEventListener('DOMContentLoaded', () => showItem('initial'));
