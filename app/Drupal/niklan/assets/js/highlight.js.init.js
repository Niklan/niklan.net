((Drupal, once) => {

  Drupal.behaviors.niklanHighlight = {
    attach(context, settings) {
      once('code-block', '[data-selector="niklan:code-block"]', context)
          .forEach(codeBlockElement => {
            const codeElement = codeBlockElement.querySelector('code');
            codeElement.classList.add('hljs');
            const worker = new Worker(settings.highlightJs.workerPath);
            worker.onmessage = (event) => {
              codeElement.innerHTML = event.data;
            }
            worker.postMessage({
              code: codeElement.textContent,
              language: codeBlockElement.dataset.language,
              libraryPath: settings.highlightJs.libraryPath,
            });
          });
    }
  }

})(Drupal, once);
