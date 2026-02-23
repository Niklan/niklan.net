/**
 * @file
 * Provides custom Drupal AJAX commands.
 *
 * These JS file injected into core/drupal.ajax library in
 * app_platform_library_info_alter().
 */
((Drupal) => {

  /**
   * Command to update the current url on request.
   *
   * This command just update URL without touch a history directly.
   *
   * @see https://developer.mozilla.org/en-US/docs/Web/API/History/replaceState
   */
  Drupal.AjaxCommands.prototype.niklanHistoryReplaceState = (ajax, response) => {
    window.history.replaceState(response.stateObj, '', response.url);
  }

})(Drupal);
