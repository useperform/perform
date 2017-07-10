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
      let id = state.order[i];
      let block = state.blocks[id];
      components.push(
        <Block key={i} block={block} />
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
