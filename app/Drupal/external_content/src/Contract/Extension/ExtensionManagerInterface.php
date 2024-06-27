<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Extension;

/**
 * {@selfdoc}
 */
interface ExtensionManagerInterface {

  /**
   * {@selfdoc}
   */
  public function get(string $extension_id): ExtensionInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $extension_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *    id: string,
   *    }
   */
  public function list(): array;

}
