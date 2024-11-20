((Drupal, once) => {

  function initAutoresize() {
    once('textarea-autoresize', '[data-textarea-autoresize]')
      .forEach(componentEl => {
        const textareaEl = componentEl.querySelector('textarea');

        if (textareaEl.length === 0) {
          return;
        }

        textareaEl.style.height = `${textareaEl.scrollHeight  }px`;

        textareaEl.addEventListener('input', () => {
          textareaEl.style.height = 'auto';
          textareaEl.style.height = `${textareaEl.scrollHeight}px`;
        });
      });
  }

  Drupal.behaviors.textarea = {
    attach(context, settings) {
      initAutoresize(settings);
    },
  };

})(Drupal, once);