<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Extension;

interface ExtensionManagerInterface {

  public function get(string $extension_id): ExtensionInterface;

  public function has(string $extension_id): bool;

  /**
   * @return array<string, array{service: string, id: string}>
   */
  public function list(): array;

}
