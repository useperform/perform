import React from 'react';

import Block from './Block';

class BlockList extends React.Component {

  render() {
    const components = [];
    for (var i=0; i < this.props.order.length; i++) {
      let id = this.props.order[i];
      let block = this.props.blocks[id];
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

export default BlockList;
