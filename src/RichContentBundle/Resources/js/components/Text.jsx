import React from 'react';

class Text extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      content: this.props.value.content
    };
  }

  onChange(e) {
    this.setState({
      content: e.currentTarget.value,
    });
  }

  finishEdit(e) {
    e.preventDefault();
    console.log('finishing edit');
  }

  render() {
    if (!this.props.editing) {
      return <p>{this.props.value.content}</p>;
    }

    return (
      <div>
        <textarea value={this.state.content} onChange={this.onChange.bind(this)} rows="10" cols="80"></textarea>
        <a href="#" className="btn btn-info" onClick={this.finishEdit}>Done</a>
      </div>
    );
  }
}

export default Text;
