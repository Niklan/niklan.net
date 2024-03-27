<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Bundler;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSourceBundleCollection;
use Drupal\external_content\Data\IdentifiedSourceCollection;

/**
 * {@selfdoc}
 */
interface BundlerManagerInterface {

  /**
   * {@selfdoc}
   */
  public function bundle(IdentifiedSourceCollection $source_collection, EnvironmentInterface $environment): IdentifiedSourceBundleCollection;

  /**
   * {@selfdoc}
   */
  public function get(string $bundler_id): BundlerInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $bundler_id): bool;

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
