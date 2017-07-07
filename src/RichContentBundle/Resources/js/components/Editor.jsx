import React from 'react';
import css from './editor.scss';
import BlockList from './BlockList';
import Toolbar from './Toolbar';

class Editor extends React.Component {
  constructor(props) {
    super(props);
  }

  componentDidMount() {
  }

  render() {
    const blocks = {
      1: {
        type: 'Text',
        value: 'Test text',
      },
      2: {
        type: 'Image',
        value: '#',
      }
    };

    const order = [
      1, 2, 1
    ];

    return (
      <div className={css.editor}>
        <Toolbar />
        <BlockList blocks={blocks} order={order} />
      </div>
    );
  }
}

export default Editor;
