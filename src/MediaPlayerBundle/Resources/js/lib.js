const storageKey = 'perform_media_player';

const onCommand = function(command, cb) {
  window.addEventListener('storage', event => {
    if (event.key !== storageKey) {
      return;
    }
    const message = JSON.parse(event.newValue);
    if (message.command !== command) {
      return;
    }
    cb(message.data);
  }, false);
}

const sendCommand = function(command, data) {
  const message = {
    timestamp: Date.now(),
    command: command,
    data: data,
  };

  window.localStorage.setItem(storageKey, JSON.stringify(message));
}

const commands = {
  openPlayer(url) {
    const windowFeatures = 'toolbar=no, location=no, status=no, menubar=no, scrollbars=no, resizable=no, width=400, height=700';
    window.open(url, 'targetWindow', windowFeatures);
  },

  startDefaultPlaylist() {
    sendCommand('startDefaultPlaylist');
  },

  startPlaylist(id) {
    sendCommand('startPlaylist', {id});
  },

  play(index) {
    sendCommand('play', {index});
  },

  pause() {
    sendCommand('pause');
  },

  stop() {
    sendCommand('stop');
  },

  seek(seconds) {
    sendCommand('seek', {seconds});
  }
};


export {
  onCommand,
  commands,
}
