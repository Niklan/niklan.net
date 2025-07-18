((Drupal, once, drupalSettings) => {

  let worker;

  function registerIntersectionObserver() {
    const intersectionObserver = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) {
          return;
        }

        const preElement = entry.target.parentElement;
        const codeElement = entry.target;
        codeElement.classList.add('hljs');
        worker.postMessage({
          code: codeElement.textContent,
          language: preElement.dataset.language,
          libraryPath: drupalSettings.highlightJs.libraryPath,
        });
        worker.onmessage = (event) => {
          codeElement.innerHTML = event.data;
        }

        intersectionObserver.unobserve(preElement);
      });
    });

    []
        .slice
        .call(once('code-highlight', 'pre > code'))
        .forEach((codeBlockElement) => {
          intersectionObserver.observe(codeBlockElement);
        });

    worker = new Worker(drupalSettings.highlightJs.workerPath);
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
