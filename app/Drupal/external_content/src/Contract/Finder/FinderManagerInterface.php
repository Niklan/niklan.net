<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Finder;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\SourceCollection;

/**
 * {@selfdoc}
 */
interface FinderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function find(EnvironmentInterface $environment): SourceCollection;

  /**
   * {@selfdoc}
   */
  public function get(string $finder_id): FinderInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $finder_id): bool;

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
