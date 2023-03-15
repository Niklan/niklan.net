<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\CodeContent;
use Drupal\content_export\Manager\MarkdownBuilderManager;

/**
 * Provides a Markdown builder for code block.
 */
final class CodeMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * Constructs a new CodeMarkdownBuilder instance.
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
    return $source instanceof CodeContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source): string {
    \assert($source instanceof CodeContent);

    if (\count($source->getFrontMatter()->getValues()) === 0) {
      // Early exist if Front Matter is empty. In that case we don't need to
      // manipulate the code block.
      return $source->getCode();
    }

    $front_matter_markdown = $this->markdownBuilderManager->buildMarkdown(
      $source->getFrontMatter(),
    );

    $code_parts = \explode(\PHP_EOL, $source->getCode());
    $first_line = \array_shift($code_parts);
    \array_unshift($code_parts, $front_matter_markdown);
    \array_unshift($code_parts, $first_line);

    return \implode(\PHP_EOL, $code_parts);
  }

}
