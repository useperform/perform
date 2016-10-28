const storageKey = 'play';

const onQueue = function(cb) {
  window.addEventListener('storage', event => {
    if (event.key !== storageKey) {
      return;
    }
    cb(event);
  }, false);
}

const queueItem = function(id) {
  window.localStorage.setItem(storageKey, id);
}

export {
  onQueue,
  queueItem
}
