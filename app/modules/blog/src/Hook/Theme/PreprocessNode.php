<?php

declare(strict_types=1);

namespace Drupal\app_blog\Hook\Theme;

use Drupal\app_blog\ExternalContent\Builder\TableOfContentsBuilder;
use Drupal\app_blog\Node\ArticleBundle;
use Drupal\app_contract\Contract\File\File;
use Drupal\app_contract\Utils\MediaHelper;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\external_content\Plugin\Field\FieldType\ExternalContentFieldItem;
use Drupal\media\MediaInterface;
use Drupal\taxonomy\TermInterface;

#[Hook('preprocess_node')]
final readonly class PreprocessNode {

  public function __construct(
    private EntityTypeManagerInterface $entityTypeManager,
  ) {}

  private function preprocessFull(ArticleBundle $node, array &$variables): void {
    $this->addEstimatedReadTime($node, $variables);
    $this->addPosterUri($node, $variables);
    $this->addAttachments($node, $variables);
    $this->addTableOfContents($node, $variables);
    $this->addTags($node, $variables);
    $this->addPreviousNext($node, $variables);
    $variables['#attached']['drupalSettings']['path']['isBlogArticlePage'] = TRUE;
  }

  private function preprocessTeaser(ArticleBundle $node, array &$variables): void {
    $this->addEstimatedReadTime($node, $variables);
    $this->addPosterUri($node, $variables);
  }

  private function preprocessPreview(ArticleBundle $node, array &$variables): void {
    $this->addPosterUri($node, $variables);
  }

  private function addEstimatedReadTime(ArticleBundle $node, array &$variables): void {
    $variables['estimated_read_time'] = $node->getEstimatedReadTime();
  }

  private function addPosterUri(ArticleBundle $node, array &$variables): void {
    $variables['poster_uri'] = MediaHelper::getFileFromMediaField(
      entity: $node,
      field_name: 'field_media_image',
    )?->getFileUri();
    // In cases where images are not properly synchronized or there is any other
    // issue, this assertion will be much more helpful than a component error.
    \assert($variables['poster_uri'] !== NULL, \sprintf('The node ID %s is missing poster image.', $node->id()));
  }

  private function addAttachments(ArticleBundle $node, array &$variables): void {
    $variables['attachments'] = [];

    foreach ($node->get('field_media_attachments') as $attachment_item) {
      $media = $attachment_item->get('entity')->getValue();
      \assert($media instanceof MediaInterface);
      $file = MediaHelper::getFile($media);

      if (!$file instanceof File) {
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

  private function addTableOfContents(ArticleBundle $node, array &$variables): void {
    if ($node->get('external_content')->isEmpty()) {
      return;
    }

    $content = $node->get('external_content')->first();
    \assert($content instanceof ExternalContentFieldItem);
    $variables['toc_links'] = (new TableOfContentsBuilder())->build($content);
  }

  private function addTags(ArticleBundle $node, array &$variables): void {
    $view_builder = $this->entityTypeManager->getViewBuilder('taxonomy_term');
    $variables['tags'] = \array_map(
      callback: static fn (TermInterface $term): array => $view_builder->view(
        entity: $term,
        view_mode: 'chip',
      ),
      array: $node->get('field_tags')->referencedEntities(),
    );
  }

  private function addPreviousNext(ArticleBundle $node, array &$variables): void {
    $variables['previous_link'] = $variables['next_link'] = NULL;

    $this->preparePreviousLink($node, $variables);
    $this->prepareNextLink($node, $variables);
  }

  private function preparePreviousNextQuery(ArticleBundle $node, string $created_operator): QueryInterface {
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

  private function preparePreviousLink(ArticleBundle $node, array &$variables): void {
    $id = $this->preparePreviousNextQuery($node, '>')->execute();

    if (!\is_array($id) || \count($id) !== 1) {
      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $id = \reset($id);
    \assert(\is_string($id));
    $previous = $storage->load($id);
    \assert($previous instanceof EntityInterface);

    $variables['previous_link'] = [
      'url' => $previous->toUrl()->toString(),
      'text' => $previous->label(),
    ];

    $cache = CacheableMetadata::createFromRenderArray($variables);
    $cache->addCacheableDependency($previous);
    $cache->applyTo($variables);
  }

  private function prepareNextLink(ArticleBundle $node, array &$variables): void {
    $cache = CacheableMetadata::createFromRenderArray($variables);
    $id = $this->preparePreviousNextQuery($node, '<')->execute();

    if (!\is_array($id) || \count($id) !== 1) {
      // Ensure it is updated when a new content is added.
      $cache->addCacheTags(['node_list:blog-entry']);
      $cache->applyTo($variables);

      return;
    }

    $storage = $this->entityTypeManager->getStorage('node');
    $id = \reset($id);
    \assert(\is_string($id));
    $next = $storage->load($id);
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

    if (!$node instanceof ArticleBundle) {
      return;
    }

    match ($variables['view_mode']) {
      'full' => $this->preprocessFull($node, $variables),
      'teaser' => $this->preprocessTeaser($node, $variables),
      'preview' => $this->preprocessPreview($node, $variables),
      default => NULL,
    };
  }

}
