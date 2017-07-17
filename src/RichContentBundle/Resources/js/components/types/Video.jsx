import React from 'react';

import getVideoId from 'get-video-id';
import {debounce} from 'lodash';

class Video extends React.Component {
  constructor(props) {
    super(props);

    this.updateUrl = debounce(url => {
      const res = getVideoId(url);
      if (!res) {
        this.updateValue(false, false);
        return;
      }
      this.updateValue(res.service, res.id);
    }, 300);
  }

  updateValue(type, id) {
    this.props.setBlockValue({
      type,
      id
    });
  }

  onInputChange(e) {
    this.updateUrl(e.currentTarget.value);
  }

  render() {
    let embed;
    switch (this.props.value.type) {
    case 'youtube':
      embed = <iframe src={`https://www.youtube.com/embed/${this.props.value.id}`} width="560" height="315" frameBorder="0" allowFullScreen></iframe>;
      break;
    case 'vimeo':
      embed = <iframe src={`https://player.vimeo.com/video/${this.props.value.id}`} width="560" height="315" frameBorder="0" allowFullScreen></iframe>
        break;
    }

    return (
      <div>
        <div>
          <input type="text" className="form-control" onChange={this.onInputChange.bind(this)} />
        </div>
        {embed}
      </div>
    )
  }
}

export default Video;
