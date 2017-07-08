import React from 'react';

class Text extends React.Component {
  render() {
    return (
      <div>
        <p>
          {this.props.value.value}
        </p>
      </div>
    );
  }
}

export default Text;
