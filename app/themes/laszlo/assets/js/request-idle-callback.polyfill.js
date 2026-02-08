/**
 * @file
 * Request Idle Callback polyfill.
 *
 * Provides requestIdleCallback fallback for browsers that don't support it.
 * When all browsers support it natively, this file can be simply removed.
 */
(() => {

  if (!window.requestIdleCallback) {
    window.requestIdleCallback = (callback) => {
      callback();
    };
  }

  if (!window.cancelIdleCallback) {
    window.cancelIdleCallback = () => {};
  }

})();
