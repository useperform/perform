import Editor from './components/Editor';
import ReactDOM from 'react-dom';
import React from 'react';

const init = function(element, config) {
  ReactDOM.render(<Editor />, element);
}

window.Perform = {
  richContent: {
    init
  }
}
