((Drupal, once) => {

  const observers = new WeakMap();
  const DEFAULT_CONFIG = {
    collapsedClass: 'is-collapsed',
    baseHeight: 400,
    buttonMargin: 60,
    observerRootMargin: '200px 0px'
  };

  const createButton = (block, { buttonClass, ariaLabel }) => {
    const button = Object.assign(document.createElement('button'), {
      className: buttonClass,
      ariaExpanded: 'false',
      textContent: Drupal.t('Expand'),
      ariaLabel: ariaLabel || Drupal.t('Expand code block')
    });

    const toggle = () => {
      block.classList.remove(block.dataset.collapsedClass);
      button.remove();
    };

    return Object.assign(button, { toggle });
  };

  const handleIntersection = (block, entries) => {
    entries
      .filter(entry => entry.isIntersecting)
      .forEach(() => {
        const observer = observers.get(block);
        observer?.unobserve(block);

        const {
          collapsedClass = DEFAULT_CONFIG.collapsedClass,
          collapsedHeight = DEFAULT_CONFIG.baseHeight,
          buttonMargin = DEFAULT_CONFIG.buttonMargin,
          collapseButtonClass: buttonClass
        } = block.dataset;

        const codeContent = block.querySelector('[data-code]');
        const contentHeight = codeContent?.scrollHeight ?? 0;
        const calculatedHeight = contentHeight - parseInt(buttonMargin, 10);

        if (calculatedHeight <= parseInt(collapsedHeight, 10)) {
          block.classList.remove(collapsedClass);
          return;
        }

        const button = createButton(block, {
          buttonClass,
          ariaLabel: codeContent?.dataset.buttonLabel
        });

        button.addEventListener('click', button.toggle);
        codeContent?.parentNode.append(button);
      });
  };

  const initCodeBlock = block => {
    const observer = new IntersectionObserver(
      entries => handleIntersection(block, entries),
      { rootMargin: DEFAULT_CONFIG.observerRootMargin }
    );

    observers.set(block, observer);
    observer.observe(block);
  };

  Drupal.behaviors.codeBlock = {
    attach(context) {
      once('code-block', '[data-selector="code-block"]', context)
        .forEach(initCodeBlock);
    },
    detach(context) {
      once.remove('code-block', '[data-selector="code-block"]', context)
        .forEach(block => {
          observers.get(block)?.disconnect();
          observers.delete(block);
        });
    }
  };

})(Drupal, once);
