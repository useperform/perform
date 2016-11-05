import React from 'react';

class ProgressBar extends React.Component {
  render() {
    return (
      <div>
        <div className="progress">
          <div className="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style={{width: `${this.props.seek}%`}}>
          </div>
        </div>
        {this.props.seek}
      </div>
    );
  }
}

export default ProgressBar;
