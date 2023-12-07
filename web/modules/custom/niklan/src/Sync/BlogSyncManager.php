<?php declare(strict_types = 1);

namespace Drupal\niklan\Sync;

use Drupal\external_content\Contract\Bundler\BundlerFacadeInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\external_content\Contract\Loader\LoaderFacadeInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginInterface;
use Drupal\external_content\Contract\Plugin\ExternalContent\Environment\EnvironmentPluginManagerInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Parser\Parser;
use Drupal\external_content\Source\File;

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
    private readonly FinderFacadeInterface $finder,
    private readonly BundlerFacadeInterface $bundler,
    private readonly Parser $parser,
    private readonly LoaderFacadeInterface $loader,
  ) {
    $this->finder->setEnvironment($this->getEnvironment());
    $this->bundler->setEnvironment($this->getEnvironment());
    $this->parser->setEnvironment($this->getEnvironment());
    $this->loader->setEnvironment($this->getEnvironment());
  }

  /**
   * {@selfdoc}
   */
  public function find(): SourceCollection {
    return $this->finder->find();
  }

  /**
   * {@selfdoc}
   */
  public function parse(File $source): Content {
    return $this->parser->parse($source);
  }

  /**
   * {@selfdoc}
   */
  public function bundle(SourceCollection $collection): SourceBundleCollection {
    return $this->bundler->bundle($collection);
  }

  /**
   * {@selfdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    return $this->loader->load($bundle);
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
