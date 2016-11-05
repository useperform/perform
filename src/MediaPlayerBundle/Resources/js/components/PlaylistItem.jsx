import React from 'react';

class PlaylistItem extends React.Component {

  click(e) {
    e.preventDefault();
    this.props.onClick(this.props.index);
  }

  render() {
    return (
      <li>
        <a href={this.props.track.url} onClick={this.click.bind(this)}>{this.props.track.name}</a>
      </li>
    );
  }
}

export default PlaylistItem;
