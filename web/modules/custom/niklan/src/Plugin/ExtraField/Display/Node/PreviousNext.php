<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Node;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;

/**
 * Previous next links.
 *
 * @ExtraFieldDisplay(
 *   id = "previous_next",
 *   label = @Translation("Previous, Next links"),
 *   bundles = {
 *     "node.*",
 *   }
 * )
 */
final class PreviousNext extends ExtraFieldDisplayBase {

  /**
   * {@inheritdoc}
   */
  public function view(ContentEntityInterface $entity): array {
    return [
      '#type' => 'niklan_previous_next',
      '#entity' => $entity,
    ];
  }

}
