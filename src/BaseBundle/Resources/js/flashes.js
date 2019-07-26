let messages = [];

let container;
function getContainer() {
  if (!container) {
    container = document.createElement('div');
    container.className = 'p-flashes'
    document.body.appendChild(container);
  }

  return container;
}

function template(data) {
  return '\
  <div id="flash-message-{id}" class="alert alert-{type} message">\
    {message}\
  </div>\
'
    .replace('{id}', data.id)
    .replace('{type}', data.type)
    .replace('{message}', data.message)
  ;
}

export function showMessage(type, message) {
  const data = {
    id: Date.now(),
    type: type,
    message: message
  };

  var element = document.createElement('div');
  element.innerHTML = template(data);
  getContainer().appendChild(element);

  //store message history for possible fanciness later
  messages.push(data);

  setTimeout(() => {
    element.classList.add('removing');
    element.addEventListener('animationend', () => {
      element.remove();
    })
  }, 3000);
}

export function showError(message) {
  return showMessage('danger', message);
}

export function showSuccess(message) {
  return showMessage('success', message);
}
