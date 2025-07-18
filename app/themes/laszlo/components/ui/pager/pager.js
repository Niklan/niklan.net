/* eslint func-names: 0 */
/**
 * Note that this 'load more' implementation assumes that all pages have the
 * same attachments. If your pages can have different sets of libraries, it
 * won't work. In that case, 'fetch' must be replaced by a proper Drupal.ajax().
 */
((window, AlpineOnce, Drupal) => {

  function pushState(url) {
    const popStateEventSubscriber = (e) => {
      window.location.href = e.state.oldUrl;
    };

    window.removeEventListener('popstate', popStateEventSubscriber);
    window.addEventListener('popstate', popStateEventSubscriber);
    // @see https://stackoverflow.com/a/28363646/4751623
    window.history.pushState({oldUrl: window.location.href}, '', url);
    window.history.pushState({oldUrl: window.location.href}, '', url);
  }

  function loadMoreReplace(searchSelector, replaceSelector, context) {
    const oldElement = document.querySelector(replaceSelector);
    const newElement = context.querySelector(searchSelector);
    if (!newElement || !oldElement) {
      return;
    }

    Drupal.detachBehaviors(oldElement);
    oldElement.replaceWith(newElement);
    Drupal.attachBehaviors(oldElement);
  }

  function loadMoreAppend(searchSelector, appendSelector, context) {
    const oldElement = document.querySelector(appendSelector);
    const newElements = context.querySelectorAll(searchSelector);
    if (!newElements || !oldElement) {
      return;
    }

    newElements.forEach(newElement => {
      oldElement.appendChild(newElement);
    });
    Drupal.attachBehaviors(oldElement);
  }

  function processLoadMoreResponse(response, settings) {
    const html = new DOMParser().parseFromString(response, 'text/html');
    Object.keys(settings.replace).forEach(search => {
      loadMoreReplace(search, settings.replace[search], html);
    });

    Object.keys(settings.append).forEach(search => {
      loadMoreAppend(search, settings.append[search], html);
    });
  }

  function isLoadMoreVisible () {
    return this.loadMoreState.visible;
  }

  function loadMore() {
    this.$refs.loadMoreButton.disabled = true;
    const url = new URL(this.$refs.loadMoreButton.dataset.loadMoreUrl, window.location);
    fetch(url)
      .then(response => response.text())
      .then(result => {
        this.pushState(url);
        this.processLoadMoreResponse(result, this.loadMoreState.settings);
      });
  }

  function init () {
    requestIdleCallback(() => {
      const settingsElement = this.$root.closest('[data-load-more-settings]');

      if (!settingsElement) {
        return;
      }

      const loadMoreSettings = JSON.parse(settingsElement.dataset.loadMoreSettings);
      this.loadMoreState.settings = loadMoreSettings;
      this.loadMoreState.visible = Object.keys(loadMoreSettings.append).length > 0 || Object.keys(loadMoreSettings.replace).length > 0;
    });
  }

  const definition = () => ({
    loadMoreState: {
      visible: false,
      settings: {},
    },

    loadMoreTemplate: {
      'x-if': function() { return this.isLoadMoreVisible() },
    },
    loadMoreButton: {
      '@click': function() { this.loadMore() },
    },

    init,
    isLoadMoreVisible,
    loadMore,
    pushState,
    processLoadMoreResponse,
  });

  AlpineOnce(() => {
    window.Alpine.data('Pager', definition);
  });

})(window, AlpineOnce, Drupal);
