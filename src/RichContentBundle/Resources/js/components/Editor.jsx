import React from 'react';
import css from './editor.scss';
import BlockList from './BlockList';
import Toolbar from './Toolbar';
import 'whatwg-fetch';
import PropTypes from 'prop-types';

class Editor extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loaded: false,
    };
  }
  getChildContext() {
    return {store: this.props.store};
  }

  componentDidMount() {
    if (true === this.state.loaded) {
      return;
    }

    const url = '/admin/_editor/content/' + this.props.contentId;
    fetch(url, {
      credentials: 'include',
    }).then(res => {
      return res.json();
    }).then(json => {
      this.setState({
        loaded: true
      });
      this.props.store.dispatch({
        type: 'CONTENT_LOAD',
        json
      });
    });
  }

  save() {
    const url = '/admin/_editor/content/save/' + this.props.contentId;
    const state = this.props.store.getState();
    const blockIds = Object.keys(state.blocks);
    let filteredBlocks = {};
    for (let i=0; i < blockIds.length; i++) {
      let block = state.blocks[blockIds[i]];
      if (!block.isNew) {
        filteredBlocks[blockIds[i]] = block;
      }
    }

    const body = {
      newBlocks: state.newBlocks,
      blocks: filteredBlocks,
      order: state.order.map(i => {
        // an array with the block id and a unique react key
        // we only want the block id
        return i[0];
      })
    };

    return fetch(url, {
      body: JSON.stringify(body),
      credentials: 'include',
      method: 'POST'
    }).then(res => {
      return res.json();
    })
  }

  addBlock() {
    this.props.store.dispatch({
      type: 'BLOCK_ADD',
      blockType: 'text'
    });
  }

  render() {
    return (
      <div className={css.editor}>
        <Toolbar save={this.save.bind(this)} add={this.addBlock.bind(this)} />
        <BlockList />
      </div>
    );
  }
}
Editor.childContextTypes = {
  store: PropTypes.object
};

export default Editor;
