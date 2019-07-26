import {showMessage, showSuccess, showError} from 'perform-base/js/flashes';

if (!window.Perform) {
  window.Perform = {};
}

Object.assign(window.Perform, {
  showMessage: showMessage,
  showSuccess: showSuccess,
  showError: showError,
});
