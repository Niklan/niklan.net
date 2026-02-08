/**
 * Color Scheme Manager
 *
 * IMPORTANT: This script requires an inline <script> in <head> to prevent FOUC
 * (Flash of Unstyled Content) when the page loads.
 *
 * The inline script must be placed BEFORE CSS loading in html.html.twig:
 *
 * <head>
 *   <script>
 *     (function() {
 *       var scheme = localStorage.getItem('color-scheme');
 *       if (scheme && (scheme === 'light' || scheme === 'dark')) {
 *         document.documentElement.setAttribute('data-theme', scheme);
 *       }
 *     })();
 *   </script>
 *   <css-placeholder>
 * </head>
 *
 * WHY: Without the inline script, users would see a brief flash of the wrong
 * theme while the page loads and this script executes. The inline script runs
 * synchronously before CSS is applied, ensuring the correct theme is set
 * immediately.
 *
 * This script then handles:
 * - User interaction with theme toggle UI
 * - Listening to system theme changes (prefers-color-scheme)
 * - Persisting user preferences to localStorage
 */
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
      const scheme = this.getColorScheme();
      const isSystemScheme = this.isSchemeFromSystem();

      this.dispatchEvent('onUpdate', scheme, isSystemScheme);

      if (isSystemScheme) {
        // Remove data-theme to let prefers-color-scheme work
        document.documentElement.removeAttribute('data-theme');
      } else {
        // Force theme via data-theme attribute
        document.documentElement.setAttribute('data-theme', this.store.getItem(this.name));
      }
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
