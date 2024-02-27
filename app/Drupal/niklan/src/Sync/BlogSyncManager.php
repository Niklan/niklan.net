<?php declare(strict_types = 1);

namespace Drupal\niklan\Sync;

use Drupal\external_content\Contract\Bundler\BundlerManagerInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderManagerInterface;
use Drupal\external_content\Contract\Loader\LoaderFacadeInterface;
use Drupal\external_content\Contract\Loader\LoaderResultInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Parser\ParserFacade;
use Drupal\external_content\Source\File;

/**
 * Provides content synchronization manager.
 *
 * @ingroup content_sync
 */
final class BlogSyncManager {

  /**
   * Constructs a new BlogContentSyncManager instance.
   */
  public function __construct(
    private readonly EnvironmentInterface $environment,
    private readonly FinderManagerInterface $finder,
    private readonly BundlerManagerInterface $bundler,
    private readonly ParserFacade $parser,
    private readonly LoaderFacadeInterface $loader,
  ) {}

  /**
   * {@selfdoc}
   */
  public function find(): SourceCollection {
    $this->finder->setEnvironment($this->environment);

    return $this->finder->find();
  }

  /**
   * {@selfdoc}
   */
  public function parse(File $source): Content {
    $this->parser->setEnvironment($this->environment);

    return $this->parser->parse($source);
  }

  /**
   * {@selfdoc}
   */
  public function bundle(SourceCollection $collection): SourceBundleCollection {
    $this->bundler->setEnvironment($this->environment);

    return $this->bundler->bundle($collection);
  }

  /**
   * {@selfdoc}
   */
  public function load(ContentBundle $bundle): LoaderResultInterface {
    $this->loader->setEnvironment($this->environment);

    return $this->loader->load($bundle);
  }

}
