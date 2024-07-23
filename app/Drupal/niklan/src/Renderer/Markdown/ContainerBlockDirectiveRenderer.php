<?php

declare(strict_types=1);

namespace Drupal\niklan\Renderer\Markdown;

/**
 * @ingroup markdown
 */
final class ContainerBlockDirectiveRenderer extends BlockDirectiveRenderer {

  #[\Override]
  protected function directiveSelector(): string {
    return 'niklan:container-directive';
  }

}
