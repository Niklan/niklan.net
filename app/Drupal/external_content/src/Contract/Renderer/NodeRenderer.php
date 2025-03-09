<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Renderer;

use Drupal\external_content\Node\RootNode;

interface NodeRenderer {

  public function render(RootNode $root): mixed;

}
