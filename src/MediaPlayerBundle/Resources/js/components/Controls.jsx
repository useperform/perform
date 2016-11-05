import React from 'react';
import PrevButton from './PrevButton';
import PlayButton from './PlayButton';
import NextButton from './NextButton';

class Controls extends React.Component {
  render() {
    return (
      <div>
        <PrevButton trackIndex={this.props.trackIndex} trackTotal={this.props.tracks.length} setTrackIndex={this.props.setTrackIndex} />
        <PlayButton playing={this.props.playing} onClick={this.props.clickPlay} />
        <NextButton trackIndex={this.props.trackIndex} trackTotal={this.props.tracks.length} setTrackIndex={this.props.setTrackIndex} />
      </div>
    );
  }
}

export default Controls;
