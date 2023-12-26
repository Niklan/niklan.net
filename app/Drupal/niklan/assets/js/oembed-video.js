/**
 * @file
 * Provides code for oembed optimizations.
 */
(function (Drupal, once) {

  /**
   * Replaces preview element with real content.
   */
  function replacePreviewWithContent(previewElement, contentTemplateElement) {
    const containerElement = previewElement.parentElement;
    const contentElement = contentTemplateElement.content.cloneNode(true);
    containerElement.appendChild(contentElement);
    previewElement.remove();
  }

  /**
   * Attaches events for OEmbed video element.
   */
  function attachEvents(OEmbedVideoElement) {
    const previewElement = OEmbedVideoElement.querySelector('[data-preview]');
    const contentTemplateElement = OEmbedVideoElement.querySelector('[data-content-template]');
    if (!previewElement || !contentTemplateElement) {
      console.error(Drupal.t('There is no preview or content for OEmbed video element:'), OEmbedVideoElement);
    }

    previewElement.addEventListener('click', () => {
      replacePreviewWithContent(previewElement, contentTemplateElement)
    }, {passive: true});
  }

  Drupal.behaviors.niklanOEmbedVideo = {
    attach(context) {
      once('oembed-video', '[data-niklan-selector="oembed-video"]', context).forEach(element => attachEvents(element));
    }
  }

})(Drupal, once);

