<?php declare(strict_types = 1);

namespace Drupal\content_export\Extractor;

use Drupal\content_export\Data\Content;
use Drupal\content_export\Data\HeadingContent;
use Drupal\content_export\Data\TextContent;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Provides an extractor for 'blog_entry' content.
 */
final class BlogEntryContentExtractor {

  /**
   * Extracts a content.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   *
   * @return \Drupal\content_export\Data\Content
   *   The content.
   */
  public function extract(BlogEntryInterface $blog_entry): Content {
    $content = new Content();

    foreach ($blog_entry->get('field_content')->referencedEntities() as $paragraph) {
      \assert($paragraph instanceof ParagraphInterface);

      match ($paragraph->bundle()) {
        default => NULL,
        // @todo important
        // @todo video
        // @todo remote_video
        // @todo image
        // @todo code
        'text' => $this->extractText($paragraph, $content),
        'heading' => $this->extractHeading($paragraph, $content),
      };
    }

    return $content;
  }

  /**
   * Extracts heading.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph entity.
   * @param \Drupal\content_export\Data\Content $content
   *   The content collection.
   */
  protected function extractHeading(ParagraphInterface $paragraph, Content $content): void {
    $level = match($paragraph->get('field_heading_level')->getString()) {
      default => 2,
      'h3' => 3,
      'h4' => 4,
      'h5' => 5,
      'h6' => 6,
    };
    $heading = $paragraph->get('field_title')->getString();

    $content->addContent(new HeadingContent($level, $heading));
  }

  /**
   * Extracts text.
   *
   * @param \Drupal\paragraphs\ParagraphInterface $paragraph
   *   The paragraph entity.
   * @param \Drupal\content_export\Data\Content $content
   *   The content collection.
   */
  protected function extractText(ParagraphInterface $paragraph, Content $content): void {
    if ($paragraph->get('field_body')->isEmpty()) {
      return;
    }

    $value = $paragraph->get('field_body')->first()->get('value')->getValue();
    $content->addContent(new TextContent($value));
  }

}
