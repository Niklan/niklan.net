<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

#[Block(
  id: 'niklan_follow_links',
  admin_label: new TranslatableMarkup('Follow Links'),
  category: new TranslatableMarkup('Custom'),
)]
final class FollowLinksBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#theme' => 'niklan_follow',
    ];
  }

}
