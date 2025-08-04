((Drupal, once, { computePosition, offset, shift, flip, inline }) => {

  const createTooltipElement = (content) => {
    const tooltip = document.createElement('div');
    tooltip.innerHTML = Drupal.theme.niklanFootnoteTooltipWrapper(content);
    return tooltip.firstElementChild;
  };

  const setupTooltipBehavior = (footnoteLink, context) => {
    const href = footnoteLink.getAttribute('href');
    const id = href.startsWith('#') ? href.substring(1) : href;
    const footnoteContent = context.querySelector(`#${CSS.escape(id)}`);

    if (!footnoteContent) return null;

    let tooltip = null;
    let hideTimeout = null;

    const cancelScheduledHide = () => {
      clearTimeout(hideTimeout);
      hideTimeout = null;
    };

    const scheduleHide = () => {
      hideTimeout = setTimeout(() => {
        tooltip?.style.setProperty('visibility', 'hidden');
        tooltip?.remove();
        tooltip = null;
      }, 300);
    };

    const setupEventListeners = (element) => {
      element.addEventListener('mouseenter', cancelScheduledHide);
      element.addEventListener('mouseleave', scheduleHide);
    };

    const updatePosition = async () => {
      if (!tooltip) {
        tooltip = createTooltipElement(footnoteContent);
        document.body.appendChild(tooltip);
        setupEventListeners(tooltip);
      }

      try {
        const { x, y } = await computePosition(footnoteLink, tooltip, {
          strategy: 'absolute',
          placement: 'top',
          middleware: [inline(), flip({ padding: 16 }), offset(16), shift({ padding: 16 })]
        });

        Object.assign(tooltip.style, {
          left: `${x}px`,
          top: `${y}px`,
          visibility: 'visible'
        });
      } catch (error) {
        console.error('Tooltip positioning failed:', error);
      }
    };

    const handleLinkEnter = () => {
      cancelScheduledHide();
      updatePosition();
    };

    footnoteLink.addEventListener('mouseenter', handleLinkEnter);
    footnoteLink.addEventListener('mouseleave', scheduleHide);

    return () => {
      footnoteLink.removeEventListener('mouseenter', handleLinkEnter);
      footnoteLink.removeEventListener('mouseleave', scheduleHide);
      tooltip?.remove();
    };
  };

  Drupal.theme.niklanFootnoteTooltipWrapper = (content) => `
    <div class="tooltip tooltip--type--footnote">
      <div class="tooltip__content">${content.innerHTML}</div>
    </div>
  `;

  Drupal.behaviors.niklanFootnoteTooltip = {
    attach: (context) => {
      const callback = () => once('niklan-footnote-tooltip', '.footnote-ref', context)
        .forEach(link => setupTooltipBehavior(link, context));

      // eslint-disable-next-line no-unused-expressions
      window.requestIdleCallback ? requestIdleCallback(callback) : callback();
    }
  };

})(Drupal, once, FloatingUIDOM);
