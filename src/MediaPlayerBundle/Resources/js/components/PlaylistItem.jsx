import React from 'react';

class PlaylistItem extends React.Component {

  click(e) {
    e.preventDefault();
    this.props.onClick(this.props.index);
  }

  render() {
    return (
      <li>
        <a href={this.props.track.url} className={this.props.active ? 'active' : ''} onClick={this.click.bind(this)}>{this.props.track.name}</a>
      </li>
    );
  }
}

export default PlaylistItem;
