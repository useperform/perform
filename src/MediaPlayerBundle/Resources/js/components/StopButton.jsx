import React from 'react';

class StopButton extends React.Component {
  render() {
    return (
        <a className="btn btn-default" onClick={this.props.onClick}>Stop</a>
    );
  }
}

export default StopButton;
