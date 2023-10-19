<?php declare(strict_types = 1);

namespace Drupal\niklan\Sync;

use Drupal\external_content\Contract\Bundler\BundlerFacadeInterface;
use Drupal\external_content\Contract\Environment\EnvironmentInterface;
use Drupal\external_content\Contract\Finder\FinderFacadeInterface;
use Drupal\external_content\Contract\Parser\HtmlParserFacadeInterface;
use Drupal\external_content\Data\ExternalContentDocumentCollection;
use Drupal\external_content\Data\ExternalContentFile;
use Drupal\niklan\Builder\BlogContentEnvironmentBuilder;

/**
 * Provides content synchronization manager.
 *
 * @ingroup content_sync
 */
final class BlogContentSyncManager {

  /**
   * Constructs a new BlogContentSyncManager instance.
   */
  public function __construct(
    private BlogContentEnvironmentBuilder $environmentBuilder,
    private FinderFacadeInterface $finder,
    private HtmlParserFacadeInterface $htmlParser,
    private BundlerFacadeInterface $bundler,
  ) {}

  /**
   * {@selfdoc}
   */
  private function getEnvironment(): EnvironmentInterface {
    return $this->environmentBuilder->build();
  }

  /**
   * Requests content synchronization.
   */
  public function synchronize(): void {
    $this->finder->setEnvironment($this->getEnvironment());
    $file_collection = $this->finder->find();

    $this->htmlParser->setEnvironment($this->getEnvironment());
    $content_documents = new ExternalContentDocumentCollection();

    foreach ($file_collection as $content_file) {
      \assert($content_file instanceof ExternalContentFile);
      $content_documents->add($this->htmlParser->parse($content_file));
    }

    // @todo Add language bundler.
    $this->bundler->setEnvironment($this->getEnvironment());
    $content_bundles = $this->bundler->bundle($content_documents);
    \dump($content_bundles);
    // @todo Build Queue and run it.
  }

}
