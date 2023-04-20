<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Builder;

use Drupal\external_content\Contract\BuilderPluginInterface;
use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Data\PlainTextElement;

/**
 * Provides a builder for plain text.
 *
 * @ExternalContentBuilder(
 *   id = "plain_text",
 *   label = @Translation("Plain text"),
 *   weight = 1000,
 * )
 */
final class PlainTextElementBuilder implements BuilderPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    return $element instanceof PlainTextElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element): array {
    \assert($element instanceof PlainTextElement);

    return [
      '#markup' => $element->getContent(),
    ];
  }

}
