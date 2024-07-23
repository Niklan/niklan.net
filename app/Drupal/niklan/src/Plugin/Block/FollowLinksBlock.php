<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a follow links block.
 *
 * @Block(
 *   id = "niklan_follow_links",
 *   admin_label = @Translation("Follow links"),
 *   category = @Translation("Custom")
 * )
 */
final class FollowLinksBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#theme' => 'niklan_follow',
    ];
  }

}
