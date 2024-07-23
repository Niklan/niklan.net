<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'niklan_node_sidebar_search_form',
  admin_label: new TranslatableMarkup('Sidebar search form'),
  category: new TranslatableMarkup('Custom'),
)]
final class SidebarSearchFormBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#theme' => 'niklan_sidebar_search_form',
    ];
  }

}
