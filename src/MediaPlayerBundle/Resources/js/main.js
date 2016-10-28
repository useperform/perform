import { onEvent } from './local-storage';

window.addEventListener('storage', onEvent, false);
