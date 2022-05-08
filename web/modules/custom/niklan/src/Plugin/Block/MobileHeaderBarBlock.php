<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a mobile header bar block.
 *
 * @Block(
 *   id = "niklan_mobile_header_bar",
 *   admin_label = @Translation("Mobile Header Bar"),
 *   category = @Translation("Custom")
 * )
 */
final class MobileHeaderBarBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'niklan_mobile_header_bar',
      '#logo' => \theme_get_setting('logo.url'),
    ];
  }

}
