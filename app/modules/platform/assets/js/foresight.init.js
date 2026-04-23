((Drupal, ForesightLib) => {

  const { origin } = window.location;
  const prefetched = new Set();

  const IGNORE_URL_PATTERNS = [
    '/user/logout',
    '/admin',
    '/edit',
    '/ajax',
    '/api',
  ];

  const IGNORE_CONTAINER_SELECTORS = [
    '#block-local-tasks-block a',
    '.block-local-tasks-block a',
    '#drupal-off-canvas a',
    '#toolbar-administration a',
  ];

  function prefetch(url) {
    if (prefetched.has(url)) {
      return;
    }
    prefetched.add(url);
    const link = document.createElement('link');
    link.rel = 'prefetch';
    link.href = url;
    document.head.appendChild(link);
  }

  function shouldSkip(anchor) {
    const { href } = anchor;
    if (!href || !href.startsWith(origin)) {
      return true;
    }
    if (anchor.hasAttribute('download') || anchor.hasAttribute('noprefetch')) {
      return true;
    }
    if (anchor.classList.contains('use-ajax')) {
      return true;
    }
    if (IGNORE_URL_PATTERNS.some((pattern) => href.includes(pattern))) {
      return true;
    }
    if (IGNORE_CONTAINER_SELECTORS.some((selector) => anchor.matches(selector))) {
      return true;
    }
    // Skip links to files (e.g. .pdf, .zip).
    if (/\.[^/?#]{1,5}([?#]|$)/.test(new URL(href).pathname)) {
      return true;
    }
    return false;
  }

  Drupal.behaviors.foresight = {
    attach(context) {
      if (!ForesightLib) {
        return;
      }

      const { ForesightManager } = ForesightLib;

      if (!ForesightManager.instance) {
        ForesightManager.initialize();
      }

      context.querySelectorAll('a[href]').forEach((anchor) => {
        if (shouldSkip(anchor)) {
          return;
        }
        ForesightManager.instance.register({
          element: anchor,
          callback: () => prefetch(anchor.href),
        });
      });
    },
  };

})(Drupal, window.ForesightLib);
