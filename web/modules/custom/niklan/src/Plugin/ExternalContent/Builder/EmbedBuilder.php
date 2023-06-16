<?php declare(strict_types = 1);

namespace Drupal\niklan\Plugin\ExternalContent\Builder;

use Drupal\external_content\Contract\BuilderPluginInterface;
use Drupal\external_content\Contract\ElementInterface;
use Drupal\external_content\Data\HtmlElement;

/**
 * Provides an embed builder.
 *
 * @ExternalContentBuilder(
 *   id = "niklan_embed",
 *   label = @Translation("Embed"),
 * )
 */
final class EmbedBuilder implements BuilderPluginInterface {

  /**
   * {@inheritdoc}
   */
  public static function isApplicable(ElementInterface $element): bool {
    if (!$element instanceof HtmlElement) {
      return FALSE;
    }

    return $element->getTag() === 'niklan-embed';
  }

  /**
   * {@inheritdoc}
   */
  public function build(ElementInterface $element, array $children): array {
    \assert($element instanceof HtmlElement);

    return [
      '#markup' => 'EMBED: ' . $element->getAttribute('href'),
    ];
  }

}
