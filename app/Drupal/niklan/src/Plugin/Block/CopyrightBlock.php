<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'niklan_copyright',
  admin_label: new TranslatableMarkup('Copyright'),
  category: new TranslatableMarkup('Custom'),
)]
final class CopyrightBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#theme' => 'niklan_copyright',
    ];
  }

}
