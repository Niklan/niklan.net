/**
 * @file
 * Provides code for highlighting the code blocks.
 */

(function (Drupal, once) {

  let trigger;

  /**
   * Registers intersection observer.
   */
  function registerIntersectionObserver() {
    const intersectionObserver = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) {
          return;
        }

        let codeBlock = entry.target;
        let codeElement = codeBlock.querySelector('pre code');
        Prism.highlightElement(codeElement);
        highlightLines(codeBlock);
        intersectionObserver.unobserve(codeBlock);
      });
    });

    [].slice.call(once('code-block', '[data-component-id="niklan:code-block"]')).forEach(function (codeBlock) {
      intersectionObserver.observe(codeBlock);
    });
  }

  /**
   * Highlights the lines if needed.
   *
   * @param codeBlock
   *   The code block element.
   */
  function highlightLines(codeBlock) {
    let preElement = codeBlock.querySelector('pre');

    if (!preElement.hasAttribute('data-highlight')) {
      return;
    }

    let highlightLines = preElement.getAttribute('data-highlight');
    let linesArray = parseHighlightLines(highlightLines);
    let totalLines = parseTotalLines(preElement);

    addHighlightElements(codeBlock, linesArray, totalLines);
  }

  function addHighlightElements(codeBlock, linesToHighlight, totalLines) {
    let codeElement = codeBlock.querySelector('pre code');
    let codeElementStyles = window.getComputedStyle(codeElement);
    let codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
    let codeLineHeight = getLineHeight(codeElement);

    let highlightClass = codeBlock.dataset.highlightedLineClass;
    let highlightElement = document.createElement('div');
    highlightElement.classList.add(highlightClass);
    highlightElement.style.height = codeLineHeight + 'px';

    linesToHighlight.forEach(lineNumber => {
      if (lineNumber <= totalLines) {
        let lineHighlightElement = highlightElement.cloneNode();
        lineHighlightElement.style.top = ((codeLineHeight * (lineNumber - 1)) + codeElementPaddingTop) + 'px';
        codeElement.appendChild(lineHighlightElement);
      }
    });
  }

  function parseTotalLines(codeBlock) {
    let codeElementStyles = window.getComputedStyle(codeBlock);
    let codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
    let codeElementPaddingBottom = parseFloat(codeElementStyles.getPropertyValue('padding-bottom'));
    let codeElementHeight = parseFloat(codeElementStyles.getPropertyValue('height'));
    let codeElementContentHeight = codeElementHeight - codeElementPaddingTop - codeElementPaddingBottom;
    let codeLineHeight = getLineHeight(codeBlock);

    return parseInt(codeElementContentHeight / codeLineHeight);
  }

  function getLineHeight(element) {
    let elementStyles = window.getComputedStyle(element);

    return parseFloat(elementStyles.getPropertyValue('line-height'));
  }

  /**
   * Parser highlight lines.
   *
   * @param highlightLines
   *   The highlighted lines.
   * @returns {*[]}
   *   Lines to highlight array.
   */
  function parseHighlightLines(highlightLines) {
    let linesArray = highlightLines.split(',');
    let linesArrayNew = [];

    linesArray.forEach(item => {
      let linesRange = item.split(':');

      if (linesRange.length === 2) {
        let lineStart = parseInt(linesRange[0]);
        let lineEnd = parseInt(linesRange[1]);
        let i;

        for (i = lineStart; i <= lineEnd; i++) {
          linesArrayNew.push(parseInt(i));
        }
      }
      else {
        linesArrayNew.push(parseInt(item));
      }
    });

    return linesArrayNew;
  }

  /**
   * Highlight the code blocks.
   *
   * We highlight only visible code blocks on the page to increase performance
   * for mobile devices on pages where a lot of code needs to be highlighted.
   */
  Drupal.behaviors.niklanCodeBlock = {
    attach: function (context, settings) {
      if (trigger) {
        return;
      }

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

      trigger(registerIntersectionObserver);
    }
  };

})(Drupal, once);
