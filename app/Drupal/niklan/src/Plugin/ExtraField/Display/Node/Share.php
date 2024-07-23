<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\ExtraField\Display\Node;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\extra_field\Plugin\ExtraFieldDisplayBase;
use Drupal\node\NodeInterface;

/**
 * @ExtraFieldDisplay(
 *   id = "share",
 *   label = @Translation("Share"),
 *   bundles = {
 *     "node.*",
 *   }
 * )
 */
final class Share extends ExtraFieldDisplayBase {

  #[\Override]
  public function view(ContentEntityInterface $entity): array {
    \assert($entity instanceof NodeInterface);

    return [
      '#theme' => 'niklan_share',
      '#url' => $entity
        ->toUrl('canonical', ['absolute' => TRUE])
        ->toString(TRUE)
        ->getGeneratedUrl(),
      '#text' => $entity->label(),
    ];
  }

}
