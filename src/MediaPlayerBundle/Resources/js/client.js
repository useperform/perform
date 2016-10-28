import { queueItem } from './local-storage';

setInterval(() => {
  queueItem(Date.now());
}, 3000);
