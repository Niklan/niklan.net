/**
 * @file
 * code-highlight.es6.js behaviors.
 */

((Drupal) => {

  /**
   * Highlight the code blocks.
   *
   * We highlight only visible code blocks on the page to increase performance
   * for mobile devices on pages where a lot of code needs to be highlighted.
   */
  Drupal.behaviors.niklanCodeHighlight = {
    attach () {
      if (!window.IntersectionObserver) {
        return;
      }

      let trigger;
      if (window.requestIdleCallback) {
        trigger = (callback) => {
          requestIdleCallback(callback)
        }
      }
      else {
        // Fallback for browsers doesn't support IDLE callbacks.
        trigger = (callback) => {
          callback()
        }
      }

      trigger(() => {
        const intersectionObserver = new IntersectionObserver((entries) => {
          entries.forEach((entry) => {
            if (entry.isIntersecting) {
              const codeBlock = entry.target;
              Prism.highlightElement(codeBlock);
              intersectionObserver.unobserve(codeBlock);
            }
          });
        });

        [].slice.call(document.querySelectorAll('pre code')).forEach((codeBlock) => {
          if (codeBlock.processed) {
            return;
          }
          codeBlock.processed = true;
          intersectionObserver.observe(codeBlock);
        });
      });
    }
  };

})(Drupal);
