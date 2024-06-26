<?php declare(strict_types = 1);

namespace Drupal\external_content\Contract\Loader;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\LoaderResultCollection;

/**
 * {@selfdoc}
 */
interface LoaderManagerInterface {

  /**
   * {@selfdoc}
   */
  public function load(IdentifiedSourceBundle $bundle, EnvironmentInterface $environment): LoaderResultCollection;

  /**
   * {@selfdoc}
   */
  public function get(string $loader_id): LoaderInterface;

  /**
   * {@selfdoc}
   */
  public function has(string $loader_id): bool;

  /**
   * {@selfdoc}
   *
   * @return array{
   *   service: string,
   *   id: string,
   *   }
   */
  public function list(): array;

}
