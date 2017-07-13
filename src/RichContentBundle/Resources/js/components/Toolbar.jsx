import React from 'react';

import {save, addBlock} from '../actions';
import PropTypes from 'prop-types';
import blockTypes from './blocktypes';

class Toolbar extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      saving: false
    };
  }

  save(e) {
    e.preventDefault();
    this.setState({
      saving: true
    });
    this.context.store.dispatch(save(this.notSaving.bind(this), this.notSaving.bind(this)));
  }

  notSaving() {
    this.setState({
      saving: false
    });
  }

  addBlock(e) {
    e.preventDefault();
    this.context.store.dispatch(addBlock(e.currentTarget.getAttribute('data-type')));
  }

  render() {
    let addBtns = Object.keys(blockTypes).map(type => {
      return (
        <a className="btn btn-primary btn-xs" href="#" onClick={this.addBlock.bind(this)} data-type={type} key={type} disabled={this.state.saving}>
          Add {type}
        </a>
      )
    });
    return (
      <div className="toolbar">
        <a className="btn btn-primary btn-xs" href="#" onClick={this.save.bind(this)} disabled={this.state.saving}>
          Save
        </a>
        {addBtns}
      </div>
    )
  }
}
Toolbar.contextTypes = {
  store: PropTypes.object
};

export default Toolbar;
