<?php declare(strict_types = 1);

namespace Drupal\content_export\Exporter;

use Drupal\content_export\Data\ExportState;
use Drupal\content_export\Extractor\BlogEntryExtractor;
use Drupal\content_export\Writer\BlogEntryWriter;
use Drupal\Core\Cache\MemoryCache\MemoryCacheInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Entity\Node\BlogEntryInterface;
use Symfony\Component\Console\Helper\ProgressBar;

/**
 * Provides an exporter for 'blog_entry' content type.
 */
final class BlogEntryExporter {

  /**
   * Constructs a new BlogEntryExporter instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Cache\MemoryCache\MemoryCacheInterface $entityMemoryCache
   *   The entity memory cache.
   * @param \Drupal\content_export\Extractor\BlogEntryExtractor $extractor
   *   The blog entry extractor.
   * @param \Drupal\content_export\Writer\BlogEntryWriter $writer
   *   The blog entry writer.
   */
  public function __construct(
    protected EntityTypeManagerInterface $entityTypeManager,
    protected MemoryCacheInterface $entityMemoryCache,
    protected BlogEntryExtractor $extractor,
    protected BlogEntryWriter $writer,
  ) {}

  /**
   * Exports a single blog entry content.
   *
   * @param \Drupal\niklan\Entity\Node\BlogEntryInterface $blog_entry
   *   The blog entry entity.
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  public function export(BlogEntryInterface $blog_entry, ExportState $state): void {
    $blog_entry_export = $this->extractor->extract($blog_entry);
    $this->writer->write($blog_entry_export, $state);
  }

  /**
   * Exports all blog entries.
   *
   * @param \Drupal\content_export\Data\ExportState $state
   *   The export state.
   */
  public function exportMultiple(ExportState $state): void {
    $blog_entry_ids = $this->getBlogEntryIds();
    $blog_entry_ids_chunks = \array_chunk(
      $blog_entry_ids,
      Settings::get('entity_update_batch_size', 50),
    );

    $progress = new ProgressBar($state->getOutput(), \count($blog_entry_ids));
    $progress->setEmptyBarCharacter('░');
    $progress->setProgressCharacter('');
    $progress->setBarCharacter('▓');
    $progress->start();

    foreach ($blog_entry_ids_chunks as $blog_entry_ids_chunk) {
      $blog_entries = $this
        ->entityTypeManager
        ->getStorage('node')
        ->loadMultiple($blog_entry_ids_chunk);

      foreach ($blog_entries as $blog_entry) {
        \assert($blog_entry instanceof BlogEntryInterface);
        $progress->advance();
        $this->export($blog_entry, $state);
      }

      $this->entityMemoryCache->deleteAll();
    }

    $progress->finish();
    $state->getOutput()->write(\PHP_EOL);
  }

  /**
   * Gets all blog entry IDs.
   *
   * @return array
   *   An array with blog entry IDs.
   */
  protected function getBlogEntryIds(): array {
    return $this
      ->entityTypeManager
      ->getStorage('node')
      ->getQuery()
      ->accessCheck(FALSE)
      ->condition('status', '1')
      ->condition('type', 'blog_entry')
      ->execute();
  }

}
