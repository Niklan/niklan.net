<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Extension;

/**
 * @template T of object
 */
interface Extension {

  /**
   * @param T $target
   */
  public function register(object $target): void;

}
