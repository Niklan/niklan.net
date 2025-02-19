<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\SourceCollection;

interface FinderManagerInterface {

  public function find(EnvironmentInterface $environment): SourceCollection;

  public function get(string $finder_id): FinderInterface;

  public function has(string $finder_id): bool;

  /**
   * @return array<string, array{service: string, id: string}>
   */
  public function list(): array;

}
