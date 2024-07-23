<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Node;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\NodeInterface;

/**
 * @ExtraFieldDisplay(
 *   id = "previous_next",
 *   label = @Translation("Previous, Next links"),
 *   bundles = {
 *     "node.*",
 *   }
 * )
 */
final class PreviousNext extends ExtraFieldDisplayBase {

  #[\Override]
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof NodeInterface);

    return [
      '#type' => 'niklan_previous_next',
      '#entity' => $entity,
    ];
  }

}
