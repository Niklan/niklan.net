<?php

declare(strict_types=1);

namespace Drupal\app_blog\Plugin\Filter;

use Drupal\Component\Utility\Html;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\RendererInterface;
use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\Template\Attribute;
use Drupal\filter\Attribute\Filter;
use Drupal\filter\FilterProcessResult;
use Drupal\filter\Plugin\FilterBase;
use Drupal\filter\Plugin\FilterInterface;
use Drupal\media\MediaInterface;
use Drupal\app_contract\Utils\MediaHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;

#[Filter(
  id: self::ID,
  title: new TranslatableMarkup('Blog media'),
  type: FilterInterface::TYPE_TRANSFORM_IRREVERSIBLE,
  description: new TranslatableMarkup('Replaces <app-media> placeholders with rendered media entities.'),
  weight: 2,
)]
final class MediaFilter extends FilterBase implements ContainerFactoryPluginInterface {

  public const string ID = 'app_blog_media';

  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    private readonly EntityTypeManagerInterface $entityTypeManager,
    private readonly RendererInterface $renderer,
  ) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  #[\Override]
  public function process($text, $langcode): FilterProcessResult {
    if (!\str_contains($text, '<app-media')) {
      return new FilterProcessResult($text);
    }

    $dom = Html::load($text);
    $elements = $this->collectByTag($dom, 'app-media');

    if (!$elements) {
      return new FilterProcessResult($text);
    }

    $uuids = [];
    foreach ($elements as $element) {
      $uuids[] = $element->getAttribute('data-uuid');
    }

    $media_entities = $this->loadMediaByUuids($uuids);
    $cache_tags = [];
    $attachments = [];

    foreach ($elements as $element) {
      [$element_tags, $element_attachments] = $this->processElement($dom, $element, $media_entities);
      $cache_tags = [...$cache_tags, ...$element_tags];
      $attachments = BubbleableMetadata::mergeAttachments($attachments, $element_attachments);
    }

    $result = new FilterProcessResult(Html::serialize($dom));
    $result->addCacheTags($cache_tags);
    $result->setAttachments(BubbleableMetadata::mergeAttachments($attachments, $result->getAttachments()));

    return $result;
  }

  #[\Override]
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): static {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get(EntityTypeManagerInterface::class),
      $container->get(RendererInterface::class),
    );
  }

  /**
   * @param array<string, \Drupal\media\MediaInterface> $media_entities
   *
   * @return array{list<string>, array<string, mixed>}
   */
  private function processElement(\DOMDocument $dom, \DOMElement $element, array $media_entities): array {
    $media = $media_entities[$element->getAttribute('data-uuid')] ?? NULL;

    if (!$media) {
      $element->parentNode?->removeChild($element);
      return [[], []];
    }

    $build = match ($element->getAttribute('data-bundle')) {
      'image' => $this->buildImage($element, $media),
      'video' => $this->buildVideo($element, $media),
      'remote_video' => $this->buildRemoteVideo($media),
      default => NULL,
    };

    if (!$build) {
      $element->parentNode?->removeChild($element);
      return [$media->getCacheTags(), []];
    }

    $this->replaceDomNode($dom, $element, (string) $this->renderer->renderInIsolation($build));
    return [$media->getCacheTags(), $build['#attached'] ?? []];
  }

  private function buildImage(\DOMElement $element, MediaInterface $media): ?array {
    $file = MediaHelper::getFile($media);
    if (!$file) {
      return NULL;
    }

    return [
      '#theme' => 'app_blog_lightbox_responsive_image',
      '#uri' => $file->getFileUri(),
      '#alt' => $element->getAttribute('data-alt') ?: NULL,
      '#thumbnail_responsive_image_style_id' => 'paragraph_image_image',
      '#lightbox_image_style_id' => 'big_image',
    ];
  }

  private function buildVideo(\DOMElement $element, MediaInterface $media): ?array {
    $file = MediaHelper::getFile($media);
    if (!$file || !$file->getMimeType()) {
      return NULL;
    }

    $source_attributes = new Attribute();
    $source_attributes->setAttribute('src', $file->createFileUrl());
    $source_attributes->setAttribute('type', $file->getMimeType());

    return [
      '#theme' => 'file_video',
      '#attributes' => $this->buildVideoAttributes($element),
      '#files' => [['file' => $file, 'source_attributes' => $source_attributes]],
    ];
  }

  /**
   * @return array<string, int|string|bool>
   */
  private function buildVideoAttributes(\DOMElement $element): array {
    $attributes = ['width' => 640, 'height' => 480];
    $title = $element->getAttribute('data-title');
    if ($title) {
      $attributes['title'] = $title;
    }

    foreach (['muted', 'autoplay', 'loop', 'controls'] as $attr) {
      if (!$element->hasAttribute($attr)) {
        continue;
      }

      $attributes[$attr] = TRUE;
    }

    return $attributes;
  }

  private function buildRemoteVideo(MediaInterface $media): array {
    return $this->entityTypeManager->getViewBuilder('media')->view($media);
  }

  /**
   * @param list<string> $uuids
   *
   * @return array<string, \Drupal\media\MediaInterface>
   */
  private function loadMediaByUuids(array $uuids): array {
    $uuids = \array_filter(\array_unique($uuids));
    if (!$uuids) {
      return [];
    }

    $storage = $this->entityTypeManager->getStorage('media');
    $ids = $storage->getQuery()
      ->accessCheck(FALSE)
      ->condition('uuid', $uuids, 'IN')
      ->execute();

    if (!$ids) {
      return [];
    }

    $result = [];
    foreach ($storage->loadMultiple($ids) as $entity) {
      $result[$entity->uuid()] = $entity;
    }

    return $result;
  }

  private function collectByTag(\DOMDocument $dom, string $tag): array {
    $elements = [];
    foreach ($dom->getElementsByTagName($tag) as $node) {
      $elements[] = $node;
    }
    return $elements;
  }

  private function replaceDomNode(\DOMDocument $dom, \DOMElement $element, string $html): void {
    $replacement = Html::load($html);
    $body = $replacement->getElementsByTagName('body')->item(0);

    if (!$body instanceof \DOMElement) {
      return;
    }

    // iterator_to_array() is required: importNode() copies without removing
    // from source, so while ($body->firstChild) would loop infinitely.
    $fragment = $dom->createDocumentFragment();
    foreach (\iterator_to_array($body->childNodes) as $child) {
      $fragment->appendChild($dom->importNode($child, TRUE));
    }

    $element->parentNode?->replaceChild($fragment, $element);
  }

}
