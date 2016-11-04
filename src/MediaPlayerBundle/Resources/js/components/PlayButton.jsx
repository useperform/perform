import React from 'react';

class PlayButton extends React.Component {
  render() {
    return (
        <a className="btn btn-default" onClick={this.props.onClick}>{this.props.playing ? 'Stop' : 'Play'}</a>
    );
  }
}

export default PlayButton;
