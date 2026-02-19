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
  };

  const handleHighlightEvent = ({ detail }) => {
    wrapLines(detail.element);
  };

  document.addEventListener(
    'niklan-highlight:highlighted',
    handleHighlightEvent,
  );
})();
