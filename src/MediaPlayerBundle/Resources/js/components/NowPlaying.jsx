import React from 'react';

class NowPlaying extends React.Component {
  render() {
    return (
      <div>
        <p>{this.props.title}</p>
      </div>
    );
  }
}

export default NowPlaying;
