<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Builder;

use Drupal\external_content\Contract\BuilderPluginInterface;
use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Data\HtmlElement;

/**
 * Provides a fenced code builder.
 *
 * @ExternalContentBuilder(
 *   id = "niklan_fenced_code",
 *   label = @Translation("Fenced code"),
 * )
 */
final class FencedCodeBuilder implements BuilderPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    if (!$element instanceof HtmlElement) {
      return FALSE;
    }

    return $element->getTag() === 'pre';
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element, array $children): array {
    \assert($element instanceof HtmlElement);

    return [
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => [
        'heading' => 'testttt',
        'code' => $children,
      ],
    ];
  }

}
