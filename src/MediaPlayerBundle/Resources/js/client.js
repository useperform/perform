import { queueItem } from './local-storage';

window.performMediaPlayer = {
  playPlaylist(id) {
    queueItem(id);
  }
};
