import React from 'react';
import css from './editor.scss';
import BlockList from './BlockList';
import Toolbar from './Toolbar';
import 'whatwg-fetch';

class Editor extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      loaded: false,
      blocks: {},
      order: [],
    };
  }

  componentDidMount() {
    if (true === this.state.loaded) {
      return;
    }

    fetch('/admin/_editor/version/1', {
      credentials: 'include',
    }).then(res => {
      return res.json();
    }).then(json => {
      this.setState({
        blocks: json.blocks,
        order: json.order,
        loaded: true
      });
    });
  }

  render() {
    return (
      <div className={css.editor}>
        <Toolbar />
        <BlockList blocks={this.state.blocks} order={this.state.order} />
      </div>
    );
  }
}

export default Editor;
