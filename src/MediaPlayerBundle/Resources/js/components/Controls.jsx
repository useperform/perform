import React from 'react';
import PlayButton from './PlayButton';

class Controls extends React.Component {
  render() {
    return (
      <div>
        <PlayButton playing={this.props.playing} onClick={this.props.clickPlay} />
      </div>
    );
  }
}

export default Controls;
