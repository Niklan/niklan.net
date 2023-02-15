<?php declare(strict_types = 1);

namespace Drupal\niklan\Element;

use Drupal\Core\Config\ImmutableConfig;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\RenderElement;
use Drupal\Core\Url;
use Drupal\media\IFrameUrlHelper;
use Drupal\media\MediaInterface;
use Drupal\media\OEmbed\Resource;
use Drupal\media\OEmbed\ResourceException;
use Drupal\media\OEmbed\ResourceFetcherInterface;
use Drupal\media\OEmbed\UrlResolverInterface;
use Drupal\responsive_image\ResponsiveImageStyleInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides render element to display OEmbed video.
 *
 * @RenderElement("niklan_oembed_video")
 */
final class OEmbedVideo extends RenderElement implements ContainerFactoryPluginInterface {

  /**
   * The oEmbed resource fetcher.
   */
  protected ResourceFetcherInterface $oEmbedFetcher;

  /**
   * The oEmbed URL resolver.
   */
  protected UrlResolverInterface $oEmbedResolver;

  /**
   * The media settings.
   */
  protected ImmutableConfig $mediaSettings;

  /**
   * The media iframe Url helper.
   */
  protected IFrameUrlHelper $iFrameUrlHelper;

  /**
   * The responsive image style storage.
   */
  protected EntityStorageInterface $responsiveImageStyleStorage;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->oEmbedFetcher = $container->get('media.oembed.resource_fetcher');
    $instance->oEmbedResolver = $container->get('media.oembed.url_resolver');
    $instance->mediaSettings = $container
      ->get('config.factory')
      ->get('media.settings');
    $instance->iFrameUrlHelper = $container
      ->get('media.oembed.iframe_url_helper');
    $instance->responsiveImageStyleStorage = $container
      ->get('entity_type.manager')
      ->getStorage('responsive_image_style');

    return $instance;
  }

  /**
   * {@inheritdoc}
   */
  public function getInfo(): array {
    return [
      '#theme' => 'niklan_oembed_video',
      '#media' => NULL,
      '#preview_responsive_image_style' => NULL,
      '#pre_render' => [
        [$this, 'preRenderOembedVideo'],
      ],
    ];
  }

  /**
   * Prepare element for rendering.
   *
   * @param array $element
   *   An array with element.
   *
   * @return array
   *   An array with modifier element.
   */
  public function preRenderOembedVideo(array $element): array {
    if (!$this->validateElement($element)) {
      return [];
    }

    $element['#preview'] = $this->buildPreview($element);
    $element['#content'] = $this->buildContent($element);

    return $element;
  }

  /**
   * Validates that element can be processed and all values are valid.
   *
   * @param array $element
   *   An array with element.
   *
   * @return bool
   *   TRUE if element is valid.
   */
  protected function validateElement(array $element): bool {
    $media = $element['#media'];

    if (!$media instanceof MediaInterface) {
      return FALSE;
    }

    if ($media->getSource()->getPluginId() !== 'oembed:video') {
      return FALSE;
    }

    $video_url = $media->getSource()->getSourceFieldValue($media);

    if (!$video_url) {
      return FALSE;
    }

    // There is no need to continued if resource didn't respond for some reason.
    if (!$this->validateResource($video_url)) {
      return FALSE;
    }

    return $this->validateResponsiveImageStyle($element);
  }

  /**
   * Validates resource availability.
   *
   * @param string $video_url
   *   The video URL.
   *
   * @return bool
   *   TRUE if resource if available, FALSE otherwise.
   */
  protected function validateResource(string $video_url): bool {
    try {
      $resource_url = $this->oEmbedResolver->getResourceUrl($video_url);
      $this->oEmbedFetcher->fetchResource($resource_url);

      return TRUE;
    }
    catch (ResourceException) {
      return FALSE;
    }
  }

  /**
   * Validates responsive image style.
   *
   * @param array $element
   *   The render element.
   *
   * @return bool
   *   TRUE if responsive image is valid, FALSE otherwise.
   */
  protected function validateResponsiveImageStyle(array $element): bool {
    if (!$element['#preview_responsive_image_style']) {
      return FALSE;
    }

    $responsive_image_style = $this->responsiveImageStyleStorage->load(
      $element['#preview_responsive_image_style'],
    );

    if (!$responsive_image_style instanceof ResponsiveImageStyleInterface) {
      return FALSE;
    }

    // Make sure this image style has mapping, without it is useless.
    return $responsive_image_style->hasImageStyleMappings();
  }

  /**
   * Builds preview for OEmbed video.
   *
   * @param array $element
   *   An array with element.
   *
   * @return array
   *   An array with preview element.
   */
  protected function buildPreview(array $element): array {
    $media = $element['#media'];
    \assert($media instanceof MediaInterface);

    return [
      '#theme' => 'responsive_image_formatter',
      '#responsive_image_style_id' => $element['#preview_responsive_image_style'],
      '#item' => $media->get('thumbnail')->first(),
      '#item_attributes' => [],
      '#cache' => [
        'tags' => $media->getCacheTags(),
      ],
    ];
  }

  /**
   * Builds content for OEmbed video.
   *
   * @param array $element
   *   An array with element.
   *
   * @return array
   *   An array with content element.
   */
  protected function buildContent(array $element): array {
    $media = $element['#media'];
    \assert($media instanceof MediaInterface);

    $video_url = $media->getSource()->getSourceFieldValue($media);
    $resource_url = $this->oEmbedResolver->getResourceUrl($video_url);
    $resource = $this->oEmbedFetcher->fetchResource($resource_url);
    $iframe_url = $this->buildIframeUrl($video_url, $resource);

    return $this->buildIframe(
      $iframe_url->toString(),
      $resource->getWidth(),
      $resource->getHeight(),
      $resource->getTitle(),
    );
  }

  /**
   * Builds iframe URL.
   *
   * @param string $url
   *   The remote video URL.
   * @param \Drupal\media\OEmbed\Resource $resource
   *   The oEmbed resource.
   */
  protected function buildIframeUrl(string $url, Resource $resource): Url {
    if ($resource->getProvider()->getName() === 'YouTube') {
      // Default controller 'media.oembed_iframe' is not used because YouTube
      // oembed provider returns iframe markup without allowing us to add
      // special query parameters like autoplay. Also, YouTube doesn't return
      // URL for that iframe.
      $video_id = $this->parseYouTubeVideoId($url);
      $url = Url::fromUri("https://www.youtube.com/embed/{$video_id}", [
        'query' => [
          'autoplay' => 1,
        ],
      ]);
    }
    else {
      // Use default behavior as fallback for Vimeo and other providers.
      $url = Url::fromRoute('media.oembed_iframe', [], [
        'query' => [
          'url' => $url,
          'hash' => $this->iFrameUrlHelper->getHash($url, 0, 0),
        ],
      ]);

      $domain = $this->mediaSettings->get('iframe_domain');

      if ($domain) {
        $url->setOption('base_url', $domain);
      }
    }

    return $url;
  }

  /**
   * Parse YouTube video ID from the URL.
   *
   * @param string $url
   *   The video URL.
   *
   * @return string|null
   *   Video ID, NULL if not found.
   */
  protected function parseYouTubeVideoId(string $url): ?string {
    \preg_match(
      "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'<> ]+)/",
      $url,
      $matches,
    );

    if (isset($matches[1])) {
      return $matches[1];
    }

    return NULL;
  }

  /**
   * Builds an iframe element.
   *
   * @param string $src
   *   The iframe URL.
   * @param int $width
   *   The iframe width.
   * @param int $height
   *   The iframe height.
   * @param string|null $title
   *   The iframe title.
   *
   * @return array
   *   An array with iframe element.
   */
  protected function buildIframe(string $src, int $width, int $height, ?string $title): array {
    $element = [
      '#type' => 'html_tag',
      '#tag' => 'iframe',
      '#attributes' => [
        'src' => $src,
        'frameborder' => 0,
        'scrolling' => FALSE,
        'allowtransparency' => TRUE,
        'allow' => 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture',
        'allowfullscreen' => TRUE,
        'width' => $width,
        'height' => $height,
        'class' => ['media-oembed-content'],
      ],
      '#attached' => [
        'library' => [
          'media/oembed.formatter',
        ],
      ],
    ];

    if ($title) {
      $element['#attributes']['title'] = $title;
    }

    return $element;
  }

}
