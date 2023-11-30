<?php declare(strict_types = 1);

namespace Drupal\niklan\Sync;

use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Finder\Finder;
use Drupal\external_content\Source\Collection;

/**
 * Provides content synchronization manager.
 *
 * @ingroup content_sync
 */
final class BlogSyncManager {

  /**
   * {@selfdoc}
   */
  private ?EnvironmentInterface $environment = NULL;

  /**
   * Constructs a new BlogContentSyncManager instance.
   */
  public function __construct(
    private readonly EnvironmentPluginManagerInterface $environmentPluginManager,
    private readonly Finder $finder,
  ) {}

  /**
   * Requests content synchronization.
   */
  public function find(): Collection {
    $this->finder->setEnvironment($this->getEnvironment());

    return $this->finder->find();
  }

  /**
   * {@selfdoc}
   */
  private function getEnvironment(): EnvironmentInterface {
    if ($this->environment) {
      return $this->environment;
    }

    $plugin = $this->environmentPluginManager->createInstance('blog');
    \assert($plugin instanceof EnvironmentPluginInterface);
    $this->environment = $plugin->getEnvironment();

    return $this->environment;
  }

}
