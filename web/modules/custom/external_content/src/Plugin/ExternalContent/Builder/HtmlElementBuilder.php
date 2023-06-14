<?php declare(strict_types = 1);

namespace Drupal\external_content\Plugin\ExternalContent\Builder;

use Drupal\Component\Utility\NestedArray;
use Drupal\external_content\Contract\BuilderPluginInterface;
use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Data\HtmlElement;

/**
 * Provides a common HTML element builder.
 *
 * @ExternalContentBuilder(
 *   id = "html",
 *   label = @Translation("HTML element"),
 *   weight = 1000,
 * )
 */
final class HtmlElementBuilder implements BuilderPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    return $element instanceof HtmlElement;
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element, array $children): array {
    \assert($element instanceof HtmlElement);

    $build = [
      '#type' => 'html_tag',
      '#tag' => $element->getTag(),
      '#attributes' => $element->getAttributes(),
    ];

    return NestedArray::mergeDeep($build, $children);
  }

}
