import React from 'react';

class Item extends React.Component {
  render() {
    return (
      <ul>
        <li>Playing item {this.props.item}</li>
      </ul>
    );
  }
}

export { Item };
