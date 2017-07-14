import React from 'react';

class Quote extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      text: this.props.value.text,
      cite: this.props.value.cite
    };
  }

  componentWillReceiveProps(newProps) {
    this.setState({
      text: this.props.value.text,
      cite: this.props.value.cite
    });
  }

  onTextChange(e) {
    this.setState({
      text: e.currentTarget.value,
    });
  }

  onCiteChange(e) {
    this.setState({
      cite: e.currentTarget.value,
    });
  }

  finishEdit(e) {
    e.preventDefault();
    this.props.setBlockValue({
      text: this.state.text,
      cite: this.state.cite,
    });
  }

  render() {
    if (!this.props.editing) {
      let inner = [<p key="0">{this.props.value.text}</p>];

      if (this.props.value.cite) {
        inner.push(
          <footer key="1">
            <cite>{this.props.value.cite}</cite>
          </footer>
        );
      }
      return (
        <blockquote>
          {inner}
        </blockquote>
      )
    }

    return (
      <div>
        <textarea placeholder="Once upon a time..." value={this.state.text} onChange={this.onTextChange.bind(this)} rows="5" cols="80"></textarea>
        <div>
          <input placeholder="Person" value={this.state.cite} onChange={this.onCiteChange.bind(this)} type="text" />
        </div>
        <a href="#" className="btn btn-info" onClick={this.finishEdit.bind(this)}>Done</a>
      </div>
    );
  }
}

export default Quote;
