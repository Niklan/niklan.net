((window, drupalSettings) => {

  let component;

  function updateBlogExtraButtonVisibility() {
    const isBlogPage = drupalSettings.path.isBlogArticlePage || false;
    const isInResponsiveMode = window.innerWidth < 1200;
    component.isBlogExtraButtonVisible = isBlogPage && isInResponsiveMode;
  }

  function registerEventListeners() {
    window.addEventListener('resize', () => {
      updateBlogExtraButtonVisibility();
    });
  }

  function init() {
    requestIdleCallback(() => {
      registerEventListeners();
      updateBlogExtraButtonVisibility();
    });
  }

  AlpineOnce(() => {
    window.Alpine.data('MainNavigation', () => ({
      init() {
        component = this;
        init();
      },
      isBlogExtraButtonVisible: false,
      isExtraMenuOpen: false,
      toggleExtraMenu() {
        this.isExtraMenuOpen = !this.isExtraMenuOpen;
      },
      closeExtraMenu() {
        this.isExtraMenuOpen = false;
      },
    }));
  });

})(window, drupalSettings);
