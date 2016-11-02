import React from 'react';

class Item extends React.Component {
  render() {
    return (
      <div>
        <h2>{this.props.json.playlist.title}</h2>
        <ul>
        {this.props.json.items.map(item => {
          return <li>{item.name} - {item.url}</li>
        })}
        </ul>
      </div>
    );
  }
}

export { Item };
