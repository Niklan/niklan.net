<?php declare(strict_types = 1);

namespace Drupal\content_export\Builder;

use Drupal\content_export\Contract\MarkdownBuilderInterface;
use Drupal\content_export\Contract\MarkdownSourceInterface;
use Drupal\content_export\Data\CodeContent;
use Drupal\content_export\Data\MarkdownBuilderState;

/**
 * Provides a Markdown builder for code block.
 */
final class CodeMarkdownBuilder implements MarkdownBuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(MarkdownSourceInterface $source): bool {
    return $source instanceof CodeContent;
  }

  /**
   * {@inheritdoc}
   */
  public function build(MarkdownSourceInterface $source, MarkdownBuilderState $state): string {
    \assert($source instanceof CodeContent);

    if (\count($source->getFrontMatter()->getValues()) === 0) {
      // Early exist if Front Matter is empty. In that case we don't need to
      // manipulate the code block.
      return $source->getCode();
    }

    // Embed front matter as JSON:
    // @code
    // ```php {"header": "example.php"}
    // echo 'Hello';
    // ```
    // @encode
    $code_parts = \explode(\PHP_EOL, $source->getCode());
    $first_line = \array_shift($code_parts);
    $first_line = \trim($first_line);
    $first_line .= ' ' . \json_encode(
      $source->getFrontMatter()->getValues(),
      \JSON_UNESCAPED_UNICODE | \JSON_UNESCAPED_SLASHES,
    );
    \array_unshift($code_parts, $first_line);

    return \implode(\PHP_EOL, $code_parts);
  }

}
