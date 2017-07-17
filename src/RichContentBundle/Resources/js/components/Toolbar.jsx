import React from 'react';

import {save, addBlock} from '../actions';
import PropTypes from 'prop-types';
import blockTypes from './blocktypes';

class Toolbar extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      saving: false,
      choosing: false,
    };
  }

  save(e) {
    e.preventDefault();
    this.setState({
      saving: true,
      choosing: false
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
    this.setState({
      choosing: false,
      blockChoice: false,
    });
    this.context.store.dispatch(addBlock(e.currentTarget.getAttribute('data-type')));
  }

  chooseBlock(e) {
    this.setState({
      choosing: !this.state.choosing,
    });
  }

  onHoverStart(e) {
    this.setState({
      blockChoice: e.currentTarget.getAttribute('data-type'),
    });
  }

  onHoverStop(e) {
    this.setState({
      blockChoice: false,
    });
  }

  render() {
    let addBtns = Object.keys(blockTypes).map(type => {
      return (
        <li onClick={this.addBlock.bind(this)} onMouseEnter={this.onHoverStart.bind(this)} onMouseLeave={this.onHoverStop.bind(this)} data-type={type} key={type}>
          {blockTypes[type].name}
        </li>
      )
    });
    return (
      <div className="toolbar">
        <a className="btn btn-primary btn-xs" href="#" onClick={this.save.bind(this)} disabled={this.state.saving}>
          Save
        </a>
        <a className="btn btn-primary btn-xs" href="#" onClick={this.chooseBlock.bind(this)} disabled={this.state.saving}>
          {this.state.choosing ? 'Cancel' : 'Add block'}
        </a>
        <div className="block-selector" style={this.state.choosing ? {} : {display: 'none'}}>
          <ul>
            {addBtns}
          </ul>
          <div className="description">
            <p>
              {this.state.blockChoice ? blockTypes[this.state.blockChoice].description : ''}
            </p>
          </div>
          <div className="clear"></div>

        </div>
      </div>
    )
  }
}
Toolbar.contextTypes = {
  store: PropTypes.object
};

export default Toolbar;
