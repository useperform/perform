import React from 'react';
import PlaylistItem from './PlaylistItem';

class Playlist extends React.Component {
  render() {
    const items = this.props.tracks.map((track, i) => {
      return <PlaylistItem key={i} index={i} track={track} onClick={this.props.clickItem}/>
    });

    return (
      <ul className="playlist">
        {items}
      </ul>
    );
  }
}

export default Playlist;
