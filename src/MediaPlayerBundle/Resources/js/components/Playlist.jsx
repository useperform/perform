import React from 'react';
import PlaylistItem from './PlaylistItem';

class Playlist extends React.Component {
  render() {
    const items = this.props.tracks.map((track, i) => {
      const active = i === this.props.trackIndex;

      return <PlaylistItem key={i} index={i} track={track} active={active} onClick={this.props.clickItem}/>
    });

    return (
      <ul className="playlist">
        {items}
      </ul>
    );
  }
}

export default Playlist;
