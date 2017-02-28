import React from 'react';

class ProgressBar extends React.Component {
  constructor(props, context) {
    super(props, context);
    this.state = {
      hoverSeconds: 0,
      hoverMargin: 0,
    }
  }

  render() {
    let ticker = '';
    if (this.state.hovering) {
      ticker = <div className="progress-ticker" style={{marginLeft: `${this.state.hoverMargin}px`}}>
        <span>{this.formatSeconds(this.state.hoverSeconds)}</span>
        </div>
    }

    const progress = this.props.seek ? (this.props.seek / this.props.duration) * 100 : 0;

    return (
      <div>
        <div className="progress"
      onMouseEnter={this.mouseEnter.bind(this)}
      onMouseLeave={this.mouseLeave.bind(this)}
      onMouseMove={this.mouseMove.bind(this)}
      onClick={this.click.bind(this)}>
        {ticker}
          <div className="progress-bar"
        role="progressbar"
        aria-valuenow="0"
        aria-valuemin="0"
        aria-valuemax="100"
        style={{width: `${progress}%`}}>
          </div>
        </div>
        {this.formatSeconds(this.props.seek)} / {this.formatSeconds(this.props.duration)}
      </div>
    );
  }

  mouseEnter(e) {
    this.setState({
      hovering: true
    });
  }

  mouseLeave(e) {
    this.setState({
      hovering: false
    });
  }

  mouseMove(e) {
    const percentage = (e.clientX - e.currentTarget.offsetLeft) / e.currentTarget.offsetWidth;
    this.setState({
      hoverSeconds: (percentage * this.props.duration).toFixed(0),
      hoverMargin: percentage * e.currentTarget.offsetWidth
    });
  }

  click(e) {
    this.props.clickSeek(this.state.hoverSeconds);
  }

  formatSeconds(s) {
    const zeroPad = function(string) {
      return (new Array(3).join('0') + string).slice(-2);
    }

    return `${zeroPad(Math.floor(s / 60))}:${zeroPad(Math.floor(s % 60))}`;
  }
}

export default ProgressBar;
