<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Renderer;

use Drupal\external_content\Node\RootNode;

interface Exporter {

  public function render(RootNode $root): mixed;

}
