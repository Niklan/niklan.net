(() => {
  const parseHighlightRanges = (highlight) => {
    const lines = new Set();

    highlight.split(',').forEach((part) => {
      const trimmed = part.trim();
      const range = trimmed.split('-');

      if (range.length === 2) {
        const start = parseInt(range[0], 10);
        const end = parseInt(range[1], 10);

        for (let i = start; i <= end; i++) {
          lines.add(i);
        }
      }
      else {
        lines.add(parseInt(trimmed, 10));
      }
    });

    return lines;
  };

  // Why JS and not CSS:
  // Highlighted lines must cover the full horizontal scroll width so the
  // background stays contiguous when the code block is scrolled. CSS
  // min-width: 100% only covers the viewport width of the scroll container,
  // not the full scroll content width. There is no CSS property that equals
  // scrollWidth. Moving overflow-x: auto to a wrapper would fix the percentage
  // resolution, but <pre> only allows phrasing content, making a <div> wrapper
  // invalid HTML, and <span> is semantically wrong as a scroll container.
  const updateHighlightedWidth = (codeElement, highlightClass) => {
    const highlighted = codeElement.querySelectorAll(`.${highlightClass}`);

    if (highlighted.length === 0) {
      return;
    }

    // Reset before measuring so the previous value doesn't affect scrollWidth.
    highlighted.forEach((span) => {
      span.style.minWidth = '';
    });

    const style = getComputedStyle(codeElement);
    const horizontalPadding =
      parseFloat(style.paddingInlineStart) + parseFloat(style.paddingInlineEnd);
    const contentWidth = codeElement.scrollWidth - horizontalPadding;

    highlighted.forEach((span) => {
      span.style.minWidth = `${contentWidth}px`;
    });
  };

  const wrapLines = (codeElement) => {
    const preElement = codeElement.parentElement;
    const { highlight } = preElement.dataset;

    if (!highlight) {
      return;
    }

    const highlightClass =
      preElement.dataset.highlightedLineClass || 'code-block__highlighted';
    const highlightedLines = parseHighlightRanges(highlight);

    const html = codeElement.innerHTML;
    const lines = html.split('\n');

    // A trailing empty line from split should not produce an extra span.
    if (lines.length > 0 && lines[lines.length - 1] === '') {
      lines.pop();
    }

    codeElement.innerHTML = lines
      .map((line, index) => {
        const lineNumber = index + 1;
        const cls = highlightedLines.has(lineNumber) ? ` class="${highlightClass}"` : '';

        return `<span${cls}>${line}</span>\n`;
      })
      .join('');

    updateHighlightedWidth(codeElement, highlightClass);

    new ResizeObserver(() => {
      updateHighlightedWidth(codeElement, highlightClass);
    }).observe(codeElement);
  };

  const handleHighlightEvent = ({ detail }) => {
    wrapLines(detail.element);
  };

  document.addEventListener(
    'niklan-highlight:highlighted',
    handleHighlightEvent,
  );
})();
