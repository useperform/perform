import React from 'react';

import blockTypes from './blocktypes';

const blockNames = Object.keys(blockTypes);

class BlockList extends React.Component {

  render() {
    const components = [];
    for (var i=0; i < this.props.order.length; i++) {
      let id = this.props.order[i];
      let block = this.props.blocks[id];
      let Tag = blockTypes[block.type];
      components.push(<Tag key={i} value={block.value} />);
    }

    return (
      <div className="block-list">
        {components}
      </div>
    )
  }
}

export default BlockList;
