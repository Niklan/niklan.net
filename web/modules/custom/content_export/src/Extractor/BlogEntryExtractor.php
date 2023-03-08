<?php declare(strict_types = 1);

namespace Drupal\content_export\Extractor;

use Drupal\content_export\Data\BlogEntryExport;
use Drupal\content_export\Data\FrontMatter;
use Drupal\niklan\Entity\Node\BlogEntryInterface;

/**
 * Provides a 'blog_entry' extractor.
 */
final class BlogEntryExtractor {

  /**
   * Extracts a single blog entry.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry.
   *
   * @return \Drupal\content_export\Data\BlogEntryExport
   *   The blog entry export.
   */
  public function extract(BlogEntryInterface $blog_entry): BlogEntryExport {
    // @todo Provide an actual logic.
    return new BlogEntryExport(new FrontMatter());
  }

}
