<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Block;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class Note extends AbstractBlock {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly string $type,
  ) {
    parent::__construct();
  }

}
