// standalone entrypoint to use the page editor outside of the perform UI.
import editPage from './edit';

if (!window.Perform) {
  window.Perform = {};
}

window.Perform.pageEditor = {
  editPage,
};
