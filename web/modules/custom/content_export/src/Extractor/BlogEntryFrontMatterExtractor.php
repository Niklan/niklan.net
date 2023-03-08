<?php declare(strict_types = 1);

namespace Drupal\content_export\Extractor;

use Drupal\content_export\Data\FrontMatter;
use Drupal\Core\Datetime\DrupalDateTime;
use Drupal\datetime\Plugin\Field\FieldType\DateTimeItemInterface;
use Drupal\niklan\Entity\Node\BlogEntryInterface;

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
    ];

    return new FrontMatter($values);
  }

}
