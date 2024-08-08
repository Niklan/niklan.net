/**
 * @file
 * Provides focus trap using Drupal API.
 */
((document, window, Drupal) => {

  /**
   * {@selfdoc}
   */
  function enableFocusTrap(element, Alpine) {
    Alpine.nextTick(() => {
      element.previousFocusElement = document.activeElement;
      element.drupalTrap = Drupal.tabbingManager.constrain(element, {trapFocus: true});
    });
  }

  /**
   * {@selfdoc}
   */
  function removeFocusTrap(element) {
    if (!element.drupalTrap) {
      return;
    }

    Drupal.tabbingManager.deactivate(element.drupalTrap);
    element.previousFocusElement.focus();
    element.previousFocusElement = null;
  }

  function drupalFocusTrap(el, { expression }, { Alpine, effect, cleanup, evaluateLater }) {
    const isActivated = evaluateLater(expression);

    effect(() => isActivated(status => {
      /* eslint no-unused-expressions: ["error", { "allowTernary": true }] */
      status ? enableFocusTrap(el, Alpine) : removeFocusTrap(el);
    }));

    cleanup(() => removeFocusTrap(el));
  }

  function register() {
    window.Alpine.directive('drupal-focus-trap', drupalFocusTrap);
  }

  if (window.Alpine) {
    register();
  }
  else {
    document.addEventListener('alpine:initializing', register);
  }

})(document, window, Drupal);
