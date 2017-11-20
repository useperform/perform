import React from 'react';

class StopButton extends React.Component {
  render() {
    return (
        <a className="btn btn-light" onClick={this.props.onClick}>Stop</a>
    );
  }
}

export default StopButton;
