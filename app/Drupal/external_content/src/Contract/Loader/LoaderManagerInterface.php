<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResultCollection;

interface LoaderManagerInterface {

  public function load(IdentifiedSourceBundle $bundle, EnvironmentInterface $environment): LoaderResultCollection;

  public function get(string $loader_id): LoaderInterface;

  public function has(string $loader_id): bool;

  /**
   * @return array<string, array{service: string, id: string}>
   */
  public function list(): array;

}
