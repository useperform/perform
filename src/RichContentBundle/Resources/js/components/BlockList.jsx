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

    for (var i=0; i < state.order.length; i++) {
      let id = state.order[i][0];
      let key = state.order[i][1];

      if (!state.blocks[id] && !state.newBlocks[id]) {
        console.error(`Unknown block ${id}`, state);
      }
      let block = id.substring(0, 1) === '_' ? state.newBlocks[id] : state.blocks[id];
      components.push(
        <Block key={key} block={block} id={id} position={i} />
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
