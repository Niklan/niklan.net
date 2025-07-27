((Drupal) => {
  const COPY_TIMEOUT = 2000;
  const STATUS_CLASSES = {
    SUCCESS: 'hljs-copy--status--success',
    ERROR: 'hljs-copy--status--error'
  };

  const createButton = () => {
    const button = document.createElement('button');
    button.className = 'hljs-copy';
    button.type = 'button';
    button.textContent = Drupal.t('Copy');
    button.dataset.copied = 'false';

    return button;
  };

  const resetButtonState = (button) => {
    button.textContent = Drupal.t('Copy');
    button.classList.remove(STATUS_CLASSES.SUCCESS, STATUS_CLASSES.ERROR);
    button.dataset.copied = 'false';
  };

  const handleCopySuccess = (button) => {
    button.textContent = Drupal.t('Copied!');
    button.classList.add(STATUS_CLASSES.SUCCESS);
    button.dataset.copied = 'true';
  };

  const handleCopyError = (button, error) => {
    console.error('Clipboard copy failed:', error);
    button.classList.add(STATUS_CLASSES.ERROR);
    button.dataset.copied = 'true';
  };

  const setupButtonClickHandler = (button, originalCode) => {
    const handleClick = async () => {
      if (button.dataset.copied === 'true') return;

      try {
        await navigator.clipboard.writeText(originalCode);
        handleCopySuccess(button);
      } catch (error) {
        handleCopyError(button, error);
      } finally {
        setTimeout(() => resetButtonState(button), COPY_TIMEOUT);
      }
    };

    button.addEventListener('click', handleClick);
  };

  const handleHighlightEvent = ({ detail }) => {
    const { element } = detail;
    const container = element.parentElement;

    if (!container.querySelector('.hljs-copy')) {
      const button = createButton();
      setupButtonClickHandler(button, detail.originalCode);
      container.appendChild(button);
    }
  };

  document.addEventListener('niklan-highlight:highlighted', handleHighlightEvent);
})(Drupal);
