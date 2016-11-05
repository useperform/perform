import React from 'react';
import Controls from './Controls';
import NowPlaying from './NowPlaying';
import Playlist from './Playlist';
import Audio from 'react-howler';
import { commands, onCommand } from '../lib';
import 'whatwg-fetch';

class Player extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {
      playing: false,
      tracks: [],
      trackIndex: 0,
      playlist: null,
    }

    this.registerStorageEvents();
  }

  startPlaylist(playlistId) {
    fetch(`/player/playlist/${playlistId}`)
      .then(response => {
        return response.json();
      }).then(json => {
        this.setState({
          playlist: json.playlist,
          tracks: json.items,
        });
        this.play(0);
      }).catch(e => {
        this.setState({playing: false, error: true});
      });
  }

  play(index) {
    if (this.state.tracks.length < 1) {
      return;
    }
    if (!index) {
      index = this.state.trackIndex;
    }
    const track = this.state.tracks[index];
    this.setState({trackIndex: index, playing: true});
  }

  stop() {
    this.setState({playing: false});
  }

  clickPlay() {
    this.setState({playing: !this.state.playing});
  }

  clickItem(index) {
    this.setState({trackIndex: index, playing: true});
  }

  render() {
    let audio = null;
    let trackName = 'No track';

    if (this.state.tracks.length > 0) {
      const track = this.state.tracks[this.state.trackIndex];
      audio = <Audio src={`/uploads/${track.url}`} playing={this.state.playing} />;
      trackName = track.name;
    }

    return (
      <div>
        <NowPlaying title={trackName} />
        <div id="progress">
        </div>
        {audio}
        <Controls playing={this.state.playing} clickPlay={this.clickPlay.bind(this)} />
        <Playlist tracks={this.state.tracks} clickItem={this.clickItem.bind(this)} />
      </div>
    );
  }

  registerStorageEvents() {
    onCommand('play', data => this.play(data.url));
    onCommand('stop', data => this.stop());
    onCommand('startPlaylist', data => this.startPlaylist(data.id));
  }
}

export default Player;
