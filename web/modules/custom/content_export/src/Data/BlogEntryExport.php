<?php declare(strict_types = 1);

namespace Drupal\content_export\Data;

/**
 * Provides a data object for extracted 'blog_entry' data.
 */
final class BlogEntryExport {

  /**
   * Constructs a new BlogEntryExport instance.
   *
   * @param \Drupal\content_export\Data\FrontMatter $frontMatter
   *   The Front Matter data.
   */
  public function __construct(
    protected FrontMatter $frontMatter,
  ) {}

}
