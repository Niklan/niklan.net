<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Extension;

use Drupal\external_content\Contract\Builder\EnvironmentBuilderInterface;

/**
 * Defines an interface for External Content Environment Extensions.
 */
interface ExtensionInterface {

  /**
   * {@selfdoc}
   */
  public function register(EnvironmentBuilderInterface $environment): void;

}
