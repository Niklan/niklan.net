<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ContentBundle;
use Drupal\external_content\Data\ContentVariation;
use Drupal\external_content\Data\IdentifiedSourceBundle;
use Drupal\external_content\Data\SourceBundleCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Data\SourceVariation;
use Drupal\external_content\Node\Content;
use Drupal\external_content\Source\File;
use Drupal\niklan\Sync\BlogSyncManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * {@selfdoc}
 *
 * @ingroup content_sync
 */
#[AsCommand(
  name: 'niklan:sync:blog',
  description: 'Synchronize blog content from a provided directory.',
)]
final class BlogSync extends Command {

  /**
   * {@selfdoc}
   */
  private SymfonyStyle $io;

  /**
   * Constructs a BlogSync object.
   */
  public function __construct(
    private readonly BlogSyncManager $syncManager,
  ) {
    parent::__construct();
  }

  /**
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new SymfonyStyle($input, $output);

    $this->io->title('Zug! Zug! Lazy peons are ready to work!');
    $sources = $this->find();
    $bundles = $this->bundle($sources);
    $this->load($bundles);

    return self::SUCCESS;
  }

  /**
   * {@selfdoc}
   */
  private function find(): SourceCollection {
    $this->io->comment('Starting sources search');

    Timer::start('finder');
    $collection = $this->syncManager->find();

    if ($collection->count()) {
      foreach (\iterator_to_array($collection) as $source) {
        \assert($source instanceof SourceInterface);
        $message = \sprintf(
          'Found source %s of type %s',
          $source->id(),
          $source->type(),
        );
        $this->io->comment($message);
      }

      $message = \sprintf(
        '%s sources found in %sms.',
        $collection->count(),
        Timer::read('finder'),
      );
      $this->io->info($message);
    }
    else {
      $this->io->note('No sources were found in a working directory.');
    }

    return $collection;
  }

  /**
   * {@selfdoc}
   */
  private function bundle(SourceCollection $sources): SourceBundleCollection {
    $this->io->comment('Start bundling sources.');
    Timer::start('bundle');
    $bundle_collection = $this->syncManager->bundle($sources);
    $message = \sprintf(
      '%s sources bundled into %s content in %sms',
      $sources->count(),
      $bundle_collection->count(),
      Timer::read('bundle'),
    );
    $this->io->info($message);

    return $bundle_collection;
  }

  /**
   * {@selfdoc}
   */
  private function load(SourceBundleCollection $bundles): void {
    $this->io->comment('Start loading sources.');
    Timer::start('load');

    foreach ($bundles as $bundle) {
      \assert($bundle instanceof IdentifiedSourceBundle);
      $this->loadBundle($bundle);
    }

    $message = \sprintf(
      '%s bundles processed in %sms',
      $bundles->count(),
      Timer::read('load'),
    );
    $this->io->info($message);
  }

  /**
   * {@selfdoc}
   */
  private function loadBundle(IdentifiedSourceBundle $bundle): void {
    $this->io->comment("Starting loading bundle '{$bundle->id}'.");
    $timer_id = "load_bundle:{$bundle->id}";
    Timer::start($timer_id);

    $parsed_bundle = $this->parseBundle($bundle);
    $this->syncManager->load($parsed_bundle);

    $this->io->comment(\sprintf(
      "Loading of bundle '%s' done in %sms.",
      $bundle->id,
      Timer::read($timer_id),
    ));
  }

  /**
   * {@selfdoc}
   */
  private function parseBundle(IdentifiedSourceBundle $source_bundle): ContentBundle {
    $content_bundle = new ContentBundle($source_bundle->id);

    foreach ($source_bundle as $source_variation) {
      \assert($source_variation instanceof SourceVariation);
      $content_variation = new ContentVariation(
        content: $this->parseSource($source_variation->source),
        attributes: $source_variation->attributes,
      );
      $content_bundle->add($content_variation);
    }

    return $content_bundle;
  }

  /**
   * {@selfdoc}
   */
  private function parseSource(SourceInterface $source): Content {
    \assert($source instanceof File);

    return $this->syncManager->parse($source);
  }

}
