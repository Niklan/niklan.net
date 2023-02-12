<?php

declare(strict_types = 1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides a sidebar search form block.
 *
 * @Block(
 *   id = "niklan_node_sidebar_search_form",
 *   admin_label = @Translation("Sidebar search form"),
 *   category = @Translation("Custom")
 * )
 */
final class SidebarSearchFormBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build(): array {
    return [
      '#theme' => 'niklan_sidebar_search_form',
    ];
  }

}
