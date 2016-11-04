import React from 'react';
import Controls from './Controls';
import NowPlaying from './NowPlaying';
import Audio from 'react-howler';
import { commands, onCommand } from '../lib';

class Player extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {
      playing: false,
      currentAudio: 'some-url',
    }

    onCommand('play', data => {
      const url = data.url ? data.url : this.state.currentAudio;
      this.setState({currentAudio: url, playing: true});
    });

    onCommand('stop', data => {
      this.setState({playing: false});
    });
  }

  clickPlay() {
    this.setState({playing: !this.state.playing});
  }

  render() {
    return (
      <div>
        <NowPlaying title={this.state.currentAudio} />
        <div id="progress">
        </div>
        <Audio src={this.state.currentAudio} playing={this.state.playing} />
        <Controls playing={this.state.playing} clickPlay={this.clickPlay.bind(this)}/>
        <div id="playlist">
        </div>
      </div>
    );
  }
}

export default Player;
