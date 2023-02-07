<?php

declare(strict_types=1);

namespace Drupal\external_content\Plugin\ExternalContent\Builder;

use Drupal\external_content\Dto\ElementInterface;
use Drupal\external_content\Dto\HtmlElement;

/**
 * Provides a common HTML element builder.
 *
 * @ExternalContentBuilder(
 *   id = "html",
 *   label = @Translation("HTML element"),
 *   weight = 1000,
 * )
 */
final class HtmlElementBuilder implements BuilderInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    return $element instanceof HtmlElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(HtmlElement|ElementInterface $element): array {
    return [
      '#type' => 'html_tag',
      '#tag' => $element->getTag(),
      '#attributes' => $element->getAttributes(),
    ];
  }

}
