<?php

declare(strict_types=1);

namespace Drupal\app_blog\Markup\Markdown\Renderer;

/**
 * @ingroup markdown
 */
final class ContainerBlockDirectiveRenderer extends BlockDirectiveRenderer {

  #[\Override]
  protected function directiveSelector(): string {
    return 'niklan:container-directive';
  }

}
