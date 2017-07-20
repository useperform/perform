import React from 'react';

import Block from './Block';
import PropTypes from 'prop-types';

class BlockList extends React.Component {
  componentDidMount() {
    const { store } = this.context;
    this.unsubscribe = store.subscribe(() => {
      this.forceUpdate();
    });
  }

  componentWillUnmount() {
    this.unsubscribe();
  }

  render() {
    const components = [];
    const { store } = this.context;
    const state = store.getState();
    const order = state.editors[this.props.editorIndex].order;

    for (let i=0; i < order.length; i++) {
      let id = order[i][0];
      let key = order[i][1];

      if (!state.blocks[id]) {
        console.error(`Unknown block ${id}`, state);
      }
      let block = state.blocks[id];
      components.push(
        <Block key={key} block={block} id={id} position={i} editorIndex={this.props.editorIndex} />
      );
    }

    return (
      <div className="block-list">
        {components}
      </div>
    )
  }
}
BlockList.contextTypes = {
  store: PropTypes.object
};

export default BlockList;
