import React from 'react';

import blockTypes from './blocktypes';
import PropTypes from 'prop-types';

class Block extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      editing: false,
    };
  }

  clickEdit() {
    this.setState({
      editing: true
    });
  }

  clickUp() {
    this.context.store.dispatch({
      type: 'BLOCK_MOVE_UP',
      currentPosition: this.props.position,
    });
  }

  clickDown() {
    this.context.store.dispatch({
      type: 'BLOCK_MOVE_DOWN',
      currentPosition: this.props.position,
    });
  }

  clickRemove() {
    this.context.store.dispatch({
      type: 'BLOCK_REMOVE',
      currentPosition: this.props.position,
    });
  }

  setBlockValue(value) {
    this.setState({
      editing: false
    });
    this.context.store.dispatch({
      type: 'BLOCK_UPDATE',
      id: this.props.id,
      value: value,
    });
  }

  render() {
    const Tag = blockTypes[this.props.block.type];
    const editClass = 'fa ' + (this.state.editing ? 'fa-times' : 'fa-pencil');

    return (
      <div>
        <div className="btn-group">
          <a className="btn btn-xs btn-default" onClick={this.clickUp.bind(this)}>
            <i className="fa fa-arrow-up"></i>
          </a>
          <a className="btn btn-xs btn-default" onClick={this.clickDown.bind(this)}>
            <i className="fa fa-arrow-down"></i>
          </a>
          <a className="btn btn-xs btn-default" onClick={this.clickEdit.bind(this)}>
            <i className={editClass}></i>
          </a>
          <a className="btn btn-xs btn-default" onClick={this.clickRemove.bind(this)}>
            <i className="fa fa-trash"></i>
          </a>
        </div>
        <Tag value={this.props.block.value} editing={this.state.editing} setBlockValue={this.setBlockValue.bind(this)} />
      </div>
    )
  }
}
Block.contextTypes = {
  store: PropTypes.object
};

export default Block;
