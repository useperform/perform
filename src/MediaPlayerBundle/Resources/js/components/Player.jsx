import React from 'react';
import Controls from './Controls';
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
      console.log('playing');
      console.log(data);
      this.setState({currentAudio: data.url, playing: true});
    });
  }

  clickPlay() {
    this.setState({playing : !this.state.playing});
  }

  render() {
    return (
      <div>
        <div id="now-playing">
        </div>
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
