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
          this.style.height = 'auto';
          this.style.height = `${this.scrollHeight  }px`;
        });
      });
  }

  Drupal.behaviors.textarea = {
    attach(context, settings) {
      initAutoresize(settings);
    },
  };

})(Drupal, once);