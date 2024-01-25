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

        const codeBlock = entry.target;
        const codeElement = codeBlock.querySelector('pre code');
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
    const preElement = codeBlock.querySelector('pre');

    if (!preElement.hasAttribute('data-highlight')) {
      return;
    }

    const highlightLines = preElement.getAttribute('data-highlight');
    const linesArray = parseHighlightLines(highlightLines);
    const totalLines = parseTotalLines(preElement);

    addHighlightElements(codeBlock, linesArray, totalLines);
  }

  function addHighlightElements(codeBlock, linesToHighlight, totalLines) {
    const codeElement = codeBlock.querySelector('pre code');
    const codeElementStyles = window.getComputedStyle(codeElement);
    const codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
    const codeLineHeight = getLineHeight(codeElement);

    const highlightClass = codeBlock.dataset.highlightedLineClass;
    const highlightElement = document.createElement('div');
    highlightElement.classList.add(highlightClass);
    highlightElement.style.height = `${codeLineHeight  }px`;

    linesToHighlight.forEach(lineNumber => {
      if (lineNumber <= totalLines) {
        const lineHighlightElement = highlightElement.cloneNode();
        lineHighlightElement.style.top = `${(codeLineHeight * (lineNumber - 1)) + codeElementPaddingTop  }px`;
        codeElement.appendChild(lineHighlightElement);
      }
    });
  }

  function parseTotalLines(codeBlock) {
    const codeElementStyles = window.getComputedStyle(codeBlock);
    const codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
    const codeElementPaddingBottom = parseFloat(codeElementStyles.getPropertyValue('padding-bottom'));
    const codeElementHeight = parseFloat(codeElementStyles.getPropertyValue('height'));
    const codeElementContentHeight = codeElementHeight - codeElementPaddingTop - codeElementPaddingBottom;
    const codeLineHeight = getLineHeight(codeBlock);

    return parseInt(codeElementContentHeight / codeLineHeight);
  }

  function getLineHeight(element) {
    const elementStyles = window.getComputedStyle(element);

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
    const linesArray = highlightLines.split(',');
    const linesArrayNew = [];

    linesArray.forEach(item => {
      const linesRange = item.split(':');

      if (linesRange.length === 2) {
        const lineStart = parseInt(linesRange[0]);
        const lineEnd = parseInt(linesRange[1]);
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
    attach (context, settings) {
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
