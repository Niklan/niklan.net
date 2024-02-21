<?php declare(strict_types = 1);

namespace Drupal\niklan\CommonMark\Renderer;

/**
 * {@selfdoc}
 *
 * @ingroup markdown
 */
final class ContainerBlockDirectiveRenderer extends BlockDirectiveRenderer {

  /**
   * {@inheritdoc}
   */
  #[\Override]
  protected function directiveSelector(): string {
    return 'niklan:container-directive';
  }

}
