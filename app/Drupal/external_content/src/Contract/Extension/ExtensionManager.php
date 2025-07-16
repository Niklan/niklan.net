<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Extension;

interface ExtensionManager {

  public function get(string $id): Extension;

  public function has(string $id): bool;

}
