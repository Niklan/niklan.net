<?php declare(strict_types = 1);

namespace Drupal\content_export\Extractor;

use Drupal\content_export\Data\BlogEntryExport;
use Drupal\niklan\Entity\Node\BlogEntryInterface;

/**
 * Provides a 'blog_entry' extractor.
 */
final class BlogEntryExtractor {

  /**
   * Constructs a new BlogEntryExtractor instance.
   *
   * @param \Drupal\content_export\Extractor\BlogEntryFrontMatterExtractor $frontMatterExtractor
   *   The Front Matter extractor.
   */
  public function __construct(
    protected BlogEntryFrontMatterExtractor $frontMatterExtractor,
  ) {}

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
    $front_matter = $this->frontMatterExtractor->extract($blog_entry);

    return new BlogEntryExport($front_matter);
  }

}
