((Drupal, once, drupalSettings) => {
  let worker;

  const createHighlightedEvent = (element, html) =>
    new CustomEvent('niklan-highlight:highlighted', {
      detail: {
        element,
        originalCode: element.textContent,
        highlightedHTML: html
      },
      bubbles: true
    });

  const handleIntersection = (entries, observer) => {
    entries.forEach(({ isIntersecting, target: codeElement }) => {
      if (!isIntersecting) return;

      const preElement = codeElement.parentElement;
      codeElement.classList.add('hljs');

      worker.postMessage({
        code: codeElement.textContent,
        language: preElement.dataset.language,
        libraryPath: drupalSettings.highlightJs.libraryPath
      });

      worker.addEventListener('message', ({ data }) => {
        codeElement.innerHTML = data;
        codeElement.dispatchEvent(createHighlightedEvent(codeElement, data));
        observer.unobserve(codeElement);
      }, { once: true });
    });
  };

  const registerIntersectionObserver = () => {
    const observer = new IntersectionObserver(handleIntersection);
    Array
      .from(once('code-highlight', 'pre > code'))
      .forEach(code => observer.observe(code));
    worker = new Worker(drupalSettings.highlightJs.workerPath);
  };

  const lazyCallback = callback =>
    window.requestIdleCallback ? requestIdleCallback(callback) : callback();

  Drupal.behaviors.niklanHighlight = {
    attach: () => lazyCallback(registerIntersectionObserver)
  };

})(Drupal, once, drupalSettings);
