<?php

declare(strict_types=1);

namespace Drupal\external_content_test\DependencyInjection;

use Drupal\external_content\Contract\Environment\EnvironmentAwareInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;

/**
 * Provides a simple class for environment aware testing.
 */
final class EnvironmentAwareClassA implements EnvironmentAwareInterface {

  private EnvironmentInterface $environment;

  public function getEnvironment(): EnvironmentInterface {
    return $this->environment;
  }

  #[\Override]
  public function setEnvironment(EnvironmentInterface $environment): void {
    $this->environment = $environment;
  }

}
