import React from 'react';
import Controls from './Controls';
import NowPlaying from './NowPlaying';
import Playlist from './Playlist';
import ProgressBar from './ProgressBar';
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
      seek: 0,
      playlist: null,
    }

    this.registerStorageEvents();
  }

  onPlay() {
    this.tickId = setInterval(this.tick.bind(this), 500);
  }

  onStop() {
    clearInterval(this.tickId);
  }

  startPlaylist(playlistId) {
    fetch(`/player/playlist/${playlistId}`)
      .then(response => {
        return response.json();
      }).then(json => {
        this.setState({
          playlist: json.playlist,
          tracks: json.items,
          trackIndex: 0,
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

  setTrackIndex(index) {
    this.setState({trackIndex: index, playing: true});
  }

  tick() {
    if (!this.state || !this.state.playing) {
      return;
    }

    const seek = this.player ? this.player.seek() : 0;
    this.setState({
      seek: seek.toFixed(0)
    });
  }

  render() {
    let audio = null;
    let trackName = 'No track';

    if (this.state.tracks.length > 0) {
      const track = this.state.tracks[this.state.trackIndex];
      audio = <Audio
      src={`/uploads/${track.url}`}
      playing={this.state.playing}
      onPlay={this.onPlay.bind(this)}
      onPause={this.onStop.bind(this)}
      ref={(ref) => this.player = ref} />;
      trackName = track.name;
    }

    return (
      <div>
        <NowPlaying title={trackName} />
        <ProgressBar seek={this.state.seek} />
        {audio}
        <Controls playing={this.state.playing} tracks={this.state.tracks} trackIndex={this.state.trackIndex} clickPlay={this.clickPlay.bind(this)} setTrackIndex={this.setTrackIndex.bind(this)} />
        <Playlist tracks={this.state.tracks} trackIndex={this.state.trackIndex} setTrackIndex={this.setTrackIndex.bind(this)} />
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
