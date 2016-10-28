import { queueItem, onQueue } from './local-storage';

onQueue(event => {
  console.log(`playing item ${event.newValue}`);
})
