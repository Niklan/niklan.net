<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Block;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerDirective extends AbstractBlock {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public string $info = '',
  ) {
    parent::__construct();
  }

}
