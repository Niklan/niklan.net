<?php

declare(strict_types=1);

namespace Drupal\external_content\Environment;

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * {@selfdoc}
 */
final class EnvironmentManager {

  /**
   * {@selfdoc}
   */
  public function __construct(
    private readonly ContainerInterface $container,
    public readonly array $environments = [],
  ) {}

}
