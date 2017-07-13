import React from 'react';

import {save} from '../actions';
import PropTypes from 'prop-types';

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

  render() {
    return (
      <div className="toolbar">
        <a className="btn btn-primary btn-xs" href="#" onClick={this.save.bind(this)} disabled={this.state.saving}>
          Save
        </a>
        <a className="btn btn-primary btn-xs" href="#" onClick={this.props.add} disabled={this.state.saving}>
          Add
        </a>
      </div>
    )
  }
}
Toolbar.contextTypes = {
  store: PropTypes.object
};

export default Toolbar;
