<?php declare(strict_types = 1);

namespace Drupal\content_export\Extractor;

use Drupal\content_export\Data\FrontMatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\file\FileInterface;
use Drupal\media\MediaInterface;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\taxonomy\TermInterface;

/**
 * Provides an extractor for 'blog_entry' Front Matter data.
 */
final class BlogEntryFrontMatterExtractor {

  /**
   * Extracts a Front Matter information.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   *
   * @return \Drupal\content_export\Data\FrontMatter
   *   The Front Matter.
   */
  public function extract(BlogEntryInterface $blog_entry): FrontMatter {
    $created = DrupalDateTime::createFromTimestamp(
      $blog_entry->getCreatedTime(),
    );

    $updated = DrupalDateTime::createFromTimestamp(
      $blog_entry->getChangedTime(),
    );

    $values = [
      'id' => $blog_entry->id(),
      'language' => $blog_entry->get('langcode')->getString(),
      'title' => $blog_entry->label(),
      'created' => $created->format(
        DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      ),
      'updated' => $updated->format(
        DateTimeItemInterface::DATETIME_STORAGE_FORMAT,
      ),
      'needs_manual_review' => TRUE,
    ];

    $this->addDescription($blog_entry, $values);
    $this->addAttachments($blog_entry, $values);
    $this->addPromo($blog_entry, $values);
    $this->addTags($blog_entry, $values);

    return new FrontMatter($values);
  }

  /**
   * Adds teaser description.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   * @param array $values
   *   An array values.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function addDescription(BlogEntryInterface $blog_entry, array &$values): void {
    if ($blog_entry->get('body')->isEmpty()) {
      return;
    }

    $values['description'] = $blog_entry
      ->get('body')
      ->first()
      ->get('value')
      ->getValue();
  }

  /**
   * Adds attachments.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   * @param array $values
   *   The values.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function addAttachments(BlogEntryInterface $blog_entry, array &$values): void {
    if ($blog_entry->get('field_media_attachments')->isEmpty()) {
      return;
    }

    foreach ($blog_entry->get('field_media_attachments')->referencedEntities() as $attachment) {
      \assert($attachment instanceof MediaInterface);
      $source_field = $attachment
        ->getSource()
        ->getConfiguration()['source_field'];
      $file = $attachment
        ->get($source_field)
        ->first()
        ->get('entity')
        ->getValue();
      \assert($file instanceof FileInterface);

      $values['attachments'][] = [
        'name' => $attachment->label(),
        'path' => $file->getFileUri(),
      ];
    }
  }

  /**
   * Adds promo image.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   * @param array $values
   *   The values.
   *
   * @throws \Drupal\Core\TypedData\Exception\MissingDataException
   */
  protected function addPromo(BlogEntryInterface $blog_entry, array &$values): void {
    if ($blog_entry->get('field_media_image')->isEmpty()) {
      return;
    }

    $media = $blog_entry
      ->get('field_media_image')
      ->first()
      ->get('entity')
      ->getValue();
    \assert($media instanceof MediaInterface);
    $image_uri = $media->getSource()->getMetadata($media, 'thumbnail_uri');
    $values['promo'] = $image_uri;
  }

  /**
   * Adds tags.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   * @param array $values
   *   The values.
   */
  protected function addTags(BlogEntryInterface $blog_entry, array &$values): void {
    if ($blog_entry->get('field_tags')->isEmpty()) {
      return;
    }

    foreach ($blog_entry->get('field_tags')->referencedEntities() as $tag) {
      \assert($tag instanceof TermInterface);
      $values['tags'][] = $tag->label();
    }
  }

}
