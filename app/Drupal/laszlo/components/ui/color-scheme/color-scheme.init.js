(() => {

  function ColorScheme() {
    this.store = localStorage;
    // The key to store value of active scheme.
    this.name = 'color-scheme';
    this.eventHandlers = {
      'onUpdate': [],
    };
    this.init();
  }

  ColorScheme.prototype = {

    init () {
      // Update theme on script initialization.
      this.update();

      // Add listener to color scheme changes.
      const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
      darkModeMediaQuery.addEventListener('change', () => {
        this.update();
      });
    },

    setColorScheme (scheme) {
      if (['system', 'light', 'dark'].includes(scheme)) {
        if (scheme ===  'system') {
          // By removing value we fallback to 'system detection' mode.
          this.store.removeItem(this.name);
        }
        else {
          this.store.setItem(this.name, scheme);
        }
      }
      else {
        // If provided not valid scheme, reset it to 'system'.
        this.store.removeItem(this.name);
      }
      this.update();
    },


    getColorScheme () {
      let currentScheme = this.store.getItem(this.name);
      // If no value is set, let it be handled by user's system.
      if (!currentScheme) {
        currentScheme = this.getColorSchemeFromSystem();
      }
      return currentScheme;
    },

    getColorSchemeFromSystem () {
      if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        return 'dark';
      }

      return 'light';

    },

    isSchemeFromSystem () {
      return this.store.getItem(this.name) === null;
    },

    update () {
      this.dispatchEvent('onUpdate', this.getColorScheme(), this.isSchemeFromSystem());
      document.documentElement.setAttribute('data-theme', this.getColorScheme());
    },

    onUpdate (handler) {
      this.eventHandlers.onUpdate.push(handler);
    },

    dispatchEvent (eventName, ...args) {
      this.eventHandlers[eventName].forEach(handler => {
        handler.call(this, ...args);
      });
    },

  }

  window.ColorScheme = new ColorScheme();

})();
