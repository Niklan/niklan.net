<?php

declare(strict_types=1);

namespace Drupal\niklan\Renderer\Markdown;

/**
 * @ingroup markdown
 */
final class LeafBlockDirectiveRenderer extends BlockDirectiveRenderer {

  #[\Override]
  protected function directiveSelector(): string {
    return 'niklan:leaf-directive';
  }

}
