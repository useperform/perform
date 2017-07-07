import React from 'react';
import css from './editor.scss';
import blockTypes from './blocktypes';

const blockNames = Object.keys(blockTypes);

class Editor extends React.Component {
  render() {
    const components = [];
    for (var i=0; i < blockNames.length; i++) {
      let Tag = blockTypes[blockNames[i]];
      components.push(<Tag key={i} />);
    }
    return (
      <div className={css.editor}>
        {components}
      </div>
    );
  }
}

export default Editor;
