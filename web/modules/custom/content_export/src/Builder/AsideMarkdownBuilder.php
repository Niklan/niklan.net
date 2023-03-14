<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\ImportantContent;
use Drupal\content_export\Manager\MarkdownBuilderManager;

/**
 * Provides a Markdown builder for Aside element (important paragraph).
 */
final class AsideMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * Constructs a new AsideMarkdownBuilder instance.
   *
   * @param \Drupal\content_export\Manager\MarkdownBuilderManager $markdownBuilderManager
   *   The Markdown builder manager.
   */
  public function __construct(
    protected MarkdownBuilderManager $markdownBuilderManager,
  ) {}

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof ImportantContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof ImportantContent);

    $inner_content_parts = [];

    foreach ($source->getContent() as $item) {
      \assert($item instanceof MarkdownSourceInterface);
      $inner_content_parts[] = $this
        ->markdownBuilderManager
        ->buildMarkdown($item);
    }

    $markdown_parts = [];
    $markdown_parts[] = '<Aside type="' . $source->getType() . '">';
    $markdown_parts[] = \PHP_EOL;
    $markdown_parts[] = \PHP_EOL;
    $markdown_parts[] = \implode(\PHP_EOL, $inner_content_parts);
    $markdown_parts[] = \PHP_EOL;
    $markdown_parts[] = \PHP_EOL;
    $markdown_parts[] = '</Aside>';

    return \implode('', $markdown_parts);
  }

}
