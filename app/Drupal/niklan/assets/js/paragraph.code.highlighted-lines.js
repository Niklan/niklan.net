/**
 * @file
 * Code highlight for paragraph code.
 *
 * @todo fix or remove.
 */

(function (Drupal) {

  Drupal.behaviors.paragraphCodeHighlightLines = {
    attach: function (context, settings) {
      let codeParagraphs = document.querySelectorAll('.paragraph-code:not(.paragraph-code--lines-highlighted)');

      if (codeParagraphs.length) {
        codeParagraphs.forEach(paragraph => {
          if (paragraph.getAttribute('data-highlighted-lines').length) {
            let linesArray = this.parseLines(paragraph.getAttribute('data-highlighted-lines'));
            let linesTotal = this.parseLinesTotal(paragraph);
            this.addHighlightElements(paragraph, linesArray, linesTotal);
          }

          paragraph.classList.add('paragraph-code--lines-highlighted');
        });
      }
    },

    /**
     * Parse line to new array.
     *
     * Handle range of lines to be correctly parsed.
     */
    parseLines: function (lines) {
      let linesArray = lines.split(',');
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
    },

    /**
     * Parse total lines in element.
     */
    parseLinesTotal: function (paragraph) {
      let codeElement = paragraph.querySelector('pre code');
      let codeElementStyles = window.getComputedStyle(codeElement);
      let codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
      let codeElementPaddingBottom = parseFloat(codeElementStyles.getPropertyValue('padding-bottom'));
      let codeElementHeight = parseFloat(codeElementStyles.getPropertyValue('height'));
      let codeElementContentHeight = codeElementHeight - codeElementPaddingTop - codeElementPaddingBottom;
      let codeLineHeight = this.getLineHeight(codeElement);

      return parseInt(codeElementContentHeight / codeLineHeight);
    },

    /**
     * Gets line height.
     */
    getLineHeight: function (element) {
      let elementStyles = window.getComputedStyle(element);

      return parseFloat(elementStyles.getPropertyValue('line-height'));
    },

    /**
     * Add elements for highlighting lines.
     */
    addHighlightElements: function (paragraph, linesToHighlight, linesTotal) {
      let codeElement = paragraph.querySelector('pre code');
      let codeElementStyles = window.getComputedStyle(codeElement);
      let codeElementPaddingTop = parseFloat(codeElementStyles.getPropertyValue('padding-top'));
      let codeLineHeight = this.getLineHeight(codeElement);

      let highlightElement = document.createElement('div');
      highlightElement.classList.add('paragraph-code__highlight-line');
      highlightElement.style.height = codeLineHeight + 'px';

      linesToHighlight.forEach(lineNumber => {
        if (lineNumber <= linesTotal) {
          let lineHighlightElement = highlightElement.cloneNode();
          lineHighlightElement.style.top = ((codeLineHeight * (lineNumber - 1)) + codeElementPaddingTop) + 'px';
          codeElement.appendChild(lineHighlightElement);
        }
      });
    }
  };

})(Drupal);
