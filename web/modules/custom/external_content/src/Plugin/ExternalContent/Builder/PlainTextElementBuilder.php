<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Builder;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\PlainTextElement;

/**
 * Provides a builder for plain text.
 *
 * @ExternalContentBuilder(
 *   id = "plain_text",
 *   label = @Translation("Plain text"),
 *   weight = 1000,
 * )
 */
final class PlainTextElementBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    return $element instanceof PlainTextElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(PlainTextElement|ElementInterface $element): array {
    return [
      '#markup' => $element->getContent(),
    ];
  }

}
