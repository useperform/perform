import React from 'react';

class PrevButton extends React.Component {
  onClick() {
    if (this.props.trackIndex === 0) {
      return;
    }
    this.props.setTrackIndex(this.props.trackIndex - 1);
  }

  render() {
    return (
        <a className="btn btn-default" onClick={this.onClick.bind(this)}>Prev</a>
    );
  }
}

export default PrevButton;
