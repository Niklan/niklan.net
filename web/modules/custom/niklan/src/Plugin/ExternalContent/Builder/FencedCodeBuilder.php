<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Builder;

use Drupal\Component\Serialization\Json;
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

    $props = [];

    if ($element->getAttribute('data-language')) {
      $props['language'] = $element->getAttribute('data-language');
    }

    if ($element->getAttribute('data-info')) {
      $info = Json::decode($element->getAttribute('data-info'));

      if (\array_key_exists('header', $info)) {
        $props['heading'] = $info['header'];
      }
    }

    return [
      '#type' => 'component',
      '#component' => 'niklan:code-block',
      '#props' => $props,
      '#slots' => [
        'code' => $children,
      ],
    ];
  }

}
