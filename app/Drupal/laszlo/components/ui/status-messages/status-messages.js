(() => {

  function addMessage(message, options = {}) {
    if (!options.hasOwnProperty('type')) {
      options.type = 'status';
    }

    if (!options.hasOwnProperty('label')) {
      switch (options.type) {
        case 'warning':
          options.label = Drupal.t('Warning');
          break;

        case 'error':
          options.label = Drupal.t('Error');
          break;

        case 'status':
        default:
          options.label = Drupal.t('Status');
          break;
      }
    }

    options.id = options.id
        ? String(options.id)
        : `${window.crypto.randomUUID()}`;

    this.messageList.push({message, options});

    if (options.hasOwnProperty('closeDelay')) {
      window.setTimeout(
        () => this.removeMessage(options.id),
        options.closeDelay,
      );
    }

    return options.id;
  }

  function removeMessage(id) {
    const index = this.messageList.findIndex((element) => element.options.id === id);

    if (index !== -1) {
      this.messageList.splice(index, 1);
    }
  }

  function init() {
    const initialMessages = JSON.parse(this.$root.dataset.messages);
    Object.keys(initialMessages).forEach(type => {
      initialMessages[type].forEach(message => {
        this.addMessage(message, { type, closeDelay: 5000 });
      });
    });
  }

  function buildClass(baseClass, message) {
    return `${baseClass  }--${  message.options.type}`;
  }

  function register() {
    Alpine.data('StatusMessages', () => ({
      messageList: [],
      init,
      addMessage,
      removeMessage,
      buildClass,
    }));
  }

  if (window.Alpine) {
    register();
  }
  else {
    document.addEventListener('alpine:initializing', register);
  }

})();
