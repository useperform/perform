import React from 'react';
import PrevButton from './PrevButton';
import PlayButton from './PlayButton';
import StopButton from './StopButton';
import NextButton from './NextButton';

class Controls extends React.Component {
  onChangeVolume(e) {
    this.props.setVolume(e.currentTarget.value);
  }

  render() {
    return (
      <div className="row">
        <div className="col-xs-8">
        <PrevButton trackIndex={this.props.trackIndex} trackTotal={this.props.tracks.length} setTrackIndex={this.props.setTrackIndex} />
        <StopButton onClick={this.props.clickStop} />
        <PlayButton playing={this.props.playing} onClick={this.props.clickPlay} />
        <NextButton trackIndex={this.props.trackIndex} trackTotal={this.props.tracks.length} setTrackIndex={this.props.setTrackIndex} />
        </div>
        <div className="col-xs-4">
        <input className="volume" type="range" step="0.01" min="0" max="1" value={this.props.volume} onChange={this.onChangeVolume.bind(this)} />
        </div>
      </div>
    );
  }
}

export default Controls;
