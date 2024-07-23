<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'niklan_mobile_header_bar',
  admin_label: new TranslatableMarkup('Mobile Header Bar'),
  category: new TranslatableMarkup('Custom'),
)]
final class MobileHeaderBarBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#theme' => 'niklan_mobile_header_bar',
      '#logo' => \theme_get_setting('logo.url'),
    ];
  }

}
