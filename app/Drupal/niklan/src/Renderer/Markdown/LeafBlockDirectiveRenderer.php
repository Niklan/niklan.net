<?php declare(strict_types = 1);

namespace Drupal\niklan\Renderer\Markdown;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class LeafBlockDirectiveRenderer extends BlockDirectiveRenderer {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function directiveSelector(): string {
    return 'niklan:leaf-directive';
  }

}
