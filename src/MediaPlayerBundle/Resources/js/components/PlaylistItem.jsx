import React from 'react';

class PlaylistItem extends React.Component {
  render() {
    return (
      <div>
        <a href="{this.props.track.url}">{this.props.track.name}</a>
      </div>
    );
  }
}

export default PlaylistItem;
