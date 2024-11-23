/**
 * This script provides a custom callback for registering Alpine-related data.
 *
 * In Drupal, we can't rely solely on the 'alpine:init' event because some
 * components and JavaScript can be delivered through an AJAX response. At that
 * time, 'alpine:init' has already been fired, so any Alpine-related code will
 * not be registered and therefore not work.
 *
 * This script is a simple workaround inspired by the 'core/once' function,
 * which solves similar issues. It calls the code immediately if Alpine is
 * already initialized, or registers that callback for the 'alpine:init' event
 * if it hasn't started yet.
 *
 * How to use:
 *
 * Old code:
 * @code
 * document.addEventListener('alpine:init', () => {
 *   Alpine.data(...)
 * })
 * @endcode
 *
 * New code:
 * @code
 * AlpineOnce(() => {
 *   Alpine.data(...)
 * })
 * @endcode
 *
 */
((window) => {

  /**
   * The initialization state of Alpine.
   *
   * The presence of 'window.Alpine' is not a reason to immediately call
   * callbacks. Alpine may still be in the process of bootstrap, which could
   * lead to a race condition. Alternatively, someone could use an approach
   * where Alpine is started manually using the 'start()' method.
   *
   * The only real sign that Alpine is ready is the 'alpine:init' event.
   */
  let isAlpineInitialized = false;
  document.addEventListener('alpine:init', () => {isAlpineInitialized = true;});

  window.AlpineOnce = (callback) => {
    if (isAlpineInitialized) {
      callback();
    }
    else {
      document.addEventListener('alpine:init', callback);
    }
  }

})(window)
