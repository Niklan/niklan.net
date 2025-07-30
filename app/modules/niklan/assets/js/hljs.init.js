((Drupal, once, drupalSettings) => {

  let worker;
  let requestId = 0;
  const pendingRequests = new Map();

  const createHighlightedEvent = (element, html) =>
    new CustomEvent('niklan-highlight:highlighted', {
      detail: {
        element,
        originalCode: element.textContent,
        highlightedHTML: html
      },
      bubbles: true
    });

  const handleIntersection = (entries) => {
    entries.forEach(({ isIntersecting, target: codeElement }) => {
      if (!isIntersecting) return;

      const preElement = codeElement.parentElement;
      codeElement.classList.add('hljs');

      const currentRequestId = requestId;
      requestId += 1;
      pendingRequests.set(currentRequestId, codeElement);

      worker.postMessage({
        requestId: currentRequestId,
        code: codeElement.textContent,
        language: preElement.dataset.language,
        esModulesBasePath: drupalSettings.highlightJs.esModulesBasePath
      });
    });
  };

  const handleWorkerMessage = ({ data }) => {
    // eslint-disable-next-line no-shadow
    const { requestId, result, error } = data;
    const codeElement = pendingRequests.get(requestId);

    if (!codeElement) return;

    if (error) {
      console.error(error);
      return;
    }

    codeElement.innerHTML = result;
    codeElement.dispatchEvent(createHighlightedEvent(codeElement, result));
    pendingRequests.delete(requestId);
  };

  const registerIntersectionObserver = () => {
    const observer = new IntersectionObserver(handleIntersection);
    Array
      .from(once('code-highlight', 'pre > code'))
      .forEach(code => observer.observe(code));

    worker = new Worker(drupalSettings.highlightJs.workerPath, { type: 'module' });
    worker.addEventListener('message', handleWorkerMessage);
  };


  const lazyCallback = callback =>
    window.requestIdleCallback ? requestIdleCallback(callback) : callback();

  Drupal.behaviors.niklanHighlight = {
    attach: () => lazyCallback(registerIntersectionObserver)
  };

})(Drupal, once, drupalSettings);
