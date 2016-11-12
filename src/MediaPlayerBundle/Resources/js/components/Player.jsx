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
      loading: false,
      tracks: [],
      trackIndex: 0,
      seek: 0,
      playlist: null,
    }

    this.registerStorageEvents();
  }

  onPlayerStart() {
    this.setState({loading: false});
    this.tickId = setInterval(this.tick.bind(this), 500);
  }

  onPlayerPause() {
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
    if (typeof index === 'undefined') {
      index = this.state.trackIndex;
    }
    const track = this.state.tracks[index];
    this.setState({trackIndex: index, playing: true, loading: true});
  }

  seekPlayer(position) {
    if (typeof this.player.seek === 'function') {
      this.player.seek(position);
    }
  }

  pause() {
    this.setState({playing: false});
  }

  stop() {
    this.seekPlayer(0);
    this.setState({playing: false, seek: 0});
  }

  clickPlay() {
    this.state.playing ? this.pause() : this.play()
  }

  setTrackIndex(index) {
    this.setState({playing: false, seek: 0}, () => {
      this.seekPlayer(0);
      this.play(index);
    });
  }

  tick() {
    if (!this.state || !this.state.playing) {
      return;
    }

    const seek = typeof this.player.seek === 'function'
            ? this.player.seek().toFixed(0)
            : 0;
    this.setState({
      seek: seek
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
      onPlay={this.onPlayerStart.bind(this)}
      onPause={this.onPlayerPause.bind(this)}
      ref={(ref) => this.player = ref} />;
      trackName = track.name;
    }

    return (
      <div>
        <NowPlaying title={trackName} loading={this.state.loading} />
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
