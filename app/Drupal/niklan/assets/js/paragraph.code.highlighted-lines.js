/**
 * @file
 * Code highlight for paragraph code.
 *
 * @todo fix or remove.
 */
((Drupal) => {

  Drupal.behaviors.paragraphCodeHighlightLines = {
    attach() {
      const codeParagraphs = document.querySelectorAll('.paragraph-code:not(.paragraph-code--lines-highlighted)');

      if (codeParagraphs.length) {
        codeParagraphs.forEach(paragraph => {
          if (paragraph.getAttribute('data-highlighted-lines').length) {
            const linesArray = this.parseLines(paragraph.getAttribute('data-highlighted-lines'));
            const linesTotal = this.parseLinesTotal(paragraph);
            this.addHighlightElements(paragraph, linesArray, linesTotal);
          }

          paragraph.classList.add('paragraph-code--lines-highlighted');
        });
      }
    },

    /**
     * Parser line to new array.
     *
     * Handle range of lines to be correctly parsed.
     */
    parseLines(lines) {
      const linesArray = lines.split(',');
      const linesArrayNew = [];

      linesArray.forEach(item => {
        const linesRange = item.split(':');

        if (linesRange.length === 2) {
          const lineStart = parseInt(linesRange[0], 10);
          const lineEnd = parseInt(linesRange[1], 10);
          let i;

          for (i = lineStart; i <= lineEnd; i++) {
            linesArrayNew.push(parseInt(i, 10));
          }
        }
        else {
          linesArrayNew.push(parseInt(item, 10));
        }
      });

      return linesArrayNew;
    },

    /**
     * Parser total lines in element.
     */
    parseLinesTotal(paragraph) {
      const codeElement = paragraph.querySelector('pre code');
      const codeElementStyles = window.getComputedStyle(codeElement);
      const codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
      const codeElementPaddingBottom = parseFloat(codeElementStyles.getPropertyValue('padding-bottom'));
      const codeElementHeight = parseFloat(codeElementStyles.getPropertyValue('height'));
      const codeElementContentHeight = codeElementHeight - codeElementPaddingTop - codeElementPaddingBottom;
      const codeLineHeight = this.getLineHeight(codeElement);

      return parseInt(codeElementContentHeight / codeLineHeight, 10);
    },

    /**
     * Gets line height.
     */
    getLineHeight(element) {
      const elementStyles = window.getComputedStyle(element);

      return parseFloat(elementStyles.getPropertyValue('line-height'));
    },

    /**
     * Add elements for highlighting lines.
     */
    addHighlightElements(paragraph, linesToHighlight, linesTotal) {
      const codeElement = paragraph.querySelector('pre code');
      const codeElementStyles = window.getComputedStyle(codeElement);
      const codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
      const codeLineHeight = this.getLineHeight(codeElement);

      const highlightElement = document.createElement('div');
      highlightElement.classList.add('paragraph-code__highlight-line');
      highlightElement.style.height = `${codeLineHeight}px`;

      linesToHighlight.forEach(lineNumber => {
        if (lineNumber <= linesTotal) {
          const lineHighlightElement = highlightElement.cloneNode();
          lineHighlightElement.style.top = `${(codeLineHeight * (lineNumber - 1)) + codeElementPaddingTop}px`;
          codeElement.appendChild(lineHighlightElement);
        }
      });
    }
  };

})(Drupal);
