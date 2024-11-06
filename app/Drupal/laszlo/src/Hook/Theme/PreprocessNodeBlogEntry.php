<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Field\FieldItemInterface;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\File\FileInterface;
use Drupal\niklan\Entity\Node\BlogEntry;
use Drupal\niklan\Helper\TocBuilder;
use Drupal\taxonomy\TermInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class PreprocessNodeBlogEntry implements ContainerInjectionInterface {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(EntityTypeManagerInterface::class),
    );
  }

  private function addFullVariables(BlogEntry $node, array &$variables): void {
    $this->addAttachments($node, $variables);
    $this->addTableOfContents($node, $variables);
    $this->addTags($node, $variables);
    $this->addPreviousNext($node, $variables);
  }

  private function addEstimatedReadTime(BlogEntry $node, array &$variables): void {
    $variables['estimated_read_time'] = $node->getEstimatedReadTime();
  }

  private function addPosterUri(BlogEntry $node, array &$variables): void {
    $variables['poster_uri'] = NULL;

    if ($node->get('field_media_image')->isEmpty()) {
      return;
    }

    $media = $node
      ->get('field_media_image')
      ->first()
      ->get('entity')
      ->getValue();

    if (!$media instanceof MediaInterface) {
      return;
    }

    $source_field = $media->getSource()->getConfiguration()['source_field'];
    $file = $media->get($source_field)->first()?->get('entity')->getValue();

    if (!$file instanceof FileInterface) {
      return;
    }

    $variables['poster_uri'] = $file->getFileUri();
  }

  private function addAttachments(BlogEntry $node, array &$variables): void {
    $variables['attachments'] = [];

    foreach ($node->get('field_media_attachments') as $attachment_item) {
      \assert($attachment_item instanceof FieldItemInterface);
      $media = $attachment_item->get('entity')->getValue();
      $source_field = $media?->getSource()->getConfiguration()['source_field'];
      $file = $media?->get($source_field)->first()?->get('entity')->getValue();

      if (!$file instanceof FileInterface) {
        continue;
      }

      $variables['attachments'][] = [
        'media_label' => $media->label(),
        'filename' => $file->getFilename(),
        'file_uri' => $file->getFileUri(),
        'size' => $file->getSize(),
        'mimetype' => $file->getMimeType(),
      ];
    }
  }

  private function addTableOfContents(BlogEntry $node, array &$variables): void {
    if ($node->get('external_content')->isEmpty()) {
      return;
    }

    $content = $node->get('external_content')->first();
    \assert($content instanceof ExternalContentFieldItem);

    $toc_builder = new TocBuilder();
    $variables['toc_links'] = $toc_builder->getTree($content);
  }

  private function addTags(BlogEntry $node, array &$variables): void {
    $view_builder = $this->entityTypeManager->getViewBuilder('taxonomy_term');
    $variables['tags'] = \array_map(
      callback: static fn (TermInterface $term): array => $view_builder->view(
        entity: $term,
        view_mode: 'chip',
      ),
      array: $node->get('field_tags')->referencedEntities(),
    );
  }

  private function addPreviousNext(BlogEntry $node, array &$variables): void {
    $variables['previous_link'] = $variables['next_link'] = NULL;

    $this->preparePreviousLink($node, $variables);
    $this->prepareNextLink($node, $variables);
  }

  private function preparePreviousNextQuery(BlogEntry $node, string $created_operator): QueryInterface {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('type', $node->bundle())
      ->condition('created', $node->getCreatedTime(), $created_operator)
      ->range(0, 1)
      ->sort('created', $created_operator === '<' ? 'DESC' : 'ASC');
  }

  private function preparePreviousLink(BlogEntry $node, array &$variables): void {
    $id = $this->preparePreviousNextQuery($node, '>')->execute();

    if (!$id) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $previous = $storage->load(\reset($id));
    \assert($previous instanceof EntityInterface);

    $variables['previous_link'] = [
      'url' => $previous->toUrl()->toString(),
      'text' => $previous->label(),
    ];

    $cache = CacheableMetadata::createFromRenderArray($variables);
    $cache->addCacheableDependency($previous);
    $cache->applyTo($variables);
  }

  private function prepareNextLink(BlogEntry $node, array &$variables): void {
    $cache = CacheableMetadata::createFromRenderArray($variables);
    $id = $this->preparePreviousNextQuery($node, '<')->execute();

    if (!$id) {
      // Ensure it is updated when a new content is added.
      $cache->addCacheTags(['node_list:blog-entry']);
      $cache->applyTo($variables);

      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $next = $storage->load(\reset($id));
    \assert($next instanceof EntityInterface);

    $variables['next_link'] = [
      'url' => $next->toUrl()->toString(),
      'text' => $next->label(),
    ];

    $cache->addCacheableDependency($next);
    $cache->applyTo($variables);
  }

  public function __invoke(array &$variables): void {
    $node = $variables['node'];
    \assert($node instanceof BlogEntry);
    $this->addEstimatedReadTime($node, $variables);
    $this->addPosterUri($node, $variables);

    match ($variables['view_mode']) {
      default => NULL,
      'full' => $this->addFullVariables($node, $variables),
    };
  }

}