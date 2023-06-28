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
   * @param \Drupal\content_export\Data\Content $content
   *   The blog entry content collection.
   */
  public function __construct(
    protected FrontMatter $frontMatter,
    protected Content $content,
  ) {}

  /**
   * Gets Front Matter.
   */
  public function getFrontMatter(): FrontMatter {
    return $this->frontMatter;
  }

  /**
   * Gets content.
   */
  public function getContent(): Content {
    return $this->content;
  }

}
