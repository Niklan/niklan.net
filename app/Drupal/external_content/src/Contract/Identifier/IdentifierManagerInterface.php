<?php

declare(strict_types=1);

namespace Drupal\external_content\Contract\Identifier;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSourceCollection;
use Drupal\external_content\Data\SourceCollection;

interface IdentifierManagerInterface {

  public function identify(SourceCollection $source_collection, EnvironmentInterface $environment): IdentifiedSourceCollection;

  public function get(string $identifier_id): IdentifierInterface;

  public function has(string $identifier_id): bool;

  /**
   * @return array{
   *   service: string,
   *    id: string,
   *    }
   */
  public function list(): array;

}
