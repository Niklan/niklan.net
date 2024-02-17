<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Block;

use League\CommonMark\Node\Block\AbstractBlock;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerBlockDirective extends AbstractBlock {

  /**
   * {@selfdoc}
   */
  public function __construct(
    public readonly int $colonLength,
    public readonly int $offset,
    public readonly string $info = '',
  ) {
    parent::__construct();
  }

}
