<?php

declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a copyright block.
 *
 * @Block(
 *   id = "niklan_copyright",
 *   admin_label = @Translation("Copyright"),
 *   category = @Translation("Custom")
 * )
 */
final class CopyrightBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'niklan_copyright',
    ];
  }

}
