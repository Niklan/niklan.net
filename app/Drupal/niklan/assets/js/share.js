/**
 * @file
 * Provides code for share button.
 */
((Drupal, once, navigator) => {

  /**
   * Attaches Share API to element.
   */
  function attachShare(shareElement) {
    const {text} = shareElement.dataset;
    const {url} = shareElement.dataset;
    if (!text || !url) {
      return;
    }

    if (!navigator.canShare || !navigator.canShare({text, url})) {
      shareElement.disabled = true;
      return;
    }

    shareElement.addEventListener('click', () => {
      navigator.share({text, url})
        // eslint-disable no-console
        .catch((error) => console.error('Sharing failed', error));
    });
  }

  Drupal.behaviors.niklanShare = {
    attach(context) {
      once('share', '[data-niklan-selector="share"]', context).forEach(element => attachShare(element));
    }
  }

})(Drupal, once, navigator);
