((Drupal, once, drupalSettings) => {

  function registerIntersectionObserver() {
    const intersectionObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const codeBlockElement = entry.target;
        const codeElement = codeBlockElement.querySelector('code');
        codeElement.classList.add('hljs');
        const worker = new Worker(drupalSettings.highlightJs.workerPath);
        worker.onmessage = (event) => {
          codeElement.innerHTML = event.data;
        }
        worker.postMessage({
          code: codeElement.textContent,
          language: codeBlockElement.dataset.language,
          libraryPath: drupalSettings.highlightJs.libraryPath,
        });

        intersectionObserver.unobserve(codeBlockElement);
      });
    });

    []
        .slice
        .call(once('code-block', '[data-selector="niklan:code-block"]'))
        .forEach((codeBlockElement) => {
          intersectionObserver.observe(codeBlockElement);
        });
  }

  function lazyCallback(callback) {
    if (window.requestIdleCallback) {
      requestIdleCallback(callback);
    }
    else {
      // Fallback for browsers doesn't support IDLE callbacks.
      callback();
    }
  }

  Drupal.behaviors.niklanHighlight = {
    attach() {
      lazyCallback(registerIntersectionObserver);
    }
  }

})(Drupal, once, drupalSettings);
