import React from 'react';
import PlaylistItem from './PlaylistItem';

class Playlist extends React.Component {
  render() {
    const items = this.props.tracks.map((track, i) => {
      return <PlaylistItem key={i} track={track} />
    });

    return (
      <div>
        {items}
      </div>
    );
  }
}

export default Playlist;
