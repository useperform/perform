import React from 'react';
import css from './editor.scss';
import BlockList from './BlockList';
import Toolbar from './Toolbar';
import 'whatwg-fetch';
import PropTypes from 'prop-types';

class Editor extends React.Component {
  getChildContext() {
    return {store: this.props.store};
  }

  render() {
    return (
      <div className={css.editor}>
        <Toolbar />
        <BlockList editorIndex={this.props.editorIndex}/>
      </div>
    );
  }
}
Editor.childContextTypes = {
  store: PropTypes.object
};

export default Editor;
