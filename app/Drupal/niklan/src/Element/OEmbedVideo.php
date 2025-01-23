<?php

declare(strict_types=1);

namespace Drupal\niklan\Element;

use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Attribute\RenderElement;
use Drupal\Core\Render\Element\RenderElementBase;
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
 * @todo Check usage, most likely not used at this point.
 */
#[RenderElement('niklan_oembed_video')]
final class OEmbedVideo extends RenderElementBase implements ContainerFactoryPluginInterface {

  protected ResourceFetcherInterface $oEmbedFetcher;
  protected UrlResolverInterface $oEmbedResolver;
  protected ConfigFactoryInterface $configFactory;
  protected IFrameUrlHelper $iFrameUrlHelper;
  protected EntityStorageInterface $responsiveImageStyleStorage;

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self {
    $instance = new self($configuration, $plugin_id, $plugin_definition);
    $instance->oEmbedFetcher = $container->get('media.oembed.resource_fetcher');
    $instance->oEmbedResolver = $container->get('media.oembed.url_resolver');
    $instance->configFactory = $container->get('config.factory');
    $instance->iFrameUrlHelper = $container
      ->get('media.oembed.iframe_url_helper');
    $instance->responsiveImageStyleStorage = $container
      ->get('entity_type.manager')
      ->getStorage('responsive_image_style');

    return $instance;
  }

  #[\Override]
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

  public function preRenderOembedVideo(array $element): array {
    if (!$this->validateElement($element)) {
      return [];
    }

    $element['#preview'] = $this->buildPreview($element);
    $element['#content'] = $this->buildContent($element);

    return $element;
  }

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

    // There is no need to continue if resource didn't respond for some reason.
    if (!$this->validateResource($video_url)) {
      return FALSE;
    }

    return $this->validateResponsiveImageStyle($element);
  }

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

  protected function buildIframeUrl(string $video_url, Resource $resource): Url {
    if ($resource->getProvider()->getName() === 'YouTube') {
      // Default controller 'media.oembed_iframe' is not used because YouTube
      // oembed provider returns iframe markup without allowing us to add
      // special query parameters like autoplay. Also, YouTube doesn't return
      // URL for that iframe.
      $video_id = $this->parseYouTubeVideoId($video_url);
      $url = Url::fromUri("https://www.youtube.com/embed/{$video_id}", [
        'query' => [
          'autoplay' => 1,
        ],
      ]);
    }
    else {
      $url = Url::fromRoute('media.oembed_iframe', [], [
        'query' => [
          'url' => $video_url,
          'hash' => $this->iFrameUrlHelper->getHash($video_url, 0, 0),
        ],
      ]);

      $domain = $this->configFactory->get('media.settings')->get('iframe_domain');

      if ($domain) {
        $url->setOption('base_url', $domain);
      }
    }

    return $url;
  }

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

  protected function parseYouTubeVideoId(string $url): ?string {
    \preg_match(
      "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'<> ]+)/",
      $url,
      $matches,
    );

    return $matches[1] ?? NULL;
  }

}
