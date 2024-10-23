<?php

declare(strict_types=1);

namespace Drupal\niklan\Plugin\Block;

use Drupal\Core\Block\Attribute\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\StringTranslation\TranslatableMarkup;

/**
 * @todo Remove.
 */
#[Block(
  id: 'niklan_node_toc',
  admin_label: new TranslatableMarkup('TOC'),
  category: new TranslatableMarkup('Custom'),
)]
final class TocBlock extends BlockBase {

  #[\Override]
  public function build(): array {
    return [
      '#markup' => '@TODO REMOVE',
    ];
  }

}
