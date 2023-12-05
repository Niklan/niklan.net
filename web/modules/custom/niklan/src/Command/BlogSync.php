<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Contract\Source\SourceInterface;
use Drupal\external_content\Data\ContentCollection;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\SourceCollection;
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
    $source_collection = $this->find();
    $content_collection = $this->parse($source_collection);
    $this->bundle($content_collection);

    return self::SUCCESS;
  }

  /**
   * {@selfdoc}
   */
  private function find(): SourceCollection {
    $this->io->comment('Starting sources search');

    Timer::start('finder');
    $collection = $this->syncManager->find();
    Timer::stop('finder');

    if ($collection->count()) {
      $message = \sprintf(
        '%s sources found in %sms.',
        $collection->count(),
        Timer::read('finder'),
      );

      if ($this->io->isVerbose()) {
        $this->io->table(
          headers: ['Source ID', 'Source Type'],
          rows: \array_map(
            static fn (SourceInterface $source): array => [
              $source->id(),
              $source->type(),
            ],
            \iterator_to_array($collection),
          ),
        );
      }

      $this->io->success($message);
    }
    else {
      $this->io->note('No sources were found in a working directory.');
    }

    return $collection;
  }

  /**
   * {@selfdoc}
   */
  private function parse(SourceCollection $collection): ContentCollection {
    $parse_statistics = [];

    $this->io->comment('Start parsing sources.');
    $this->io->progressStart($collection->count());
    Timer::start('parse');
    $content_collection = new ContentCollection();

    foreach ($this->io->progressIterate($collection) as $source) {
      \assert($source instanceof File);
      $timer_id = "parse_{$source->id()}";
      Timer::start($timer_id);
      $content_collection->add($this->syncManager->parse($source));
      Timer::stop($timer_id);
      $this->io->progressAdvance();
      $parse_statistics[] = [$source->id(), Timer::read($timer_id)];
    }

    Timer::stop('parse');
    $this->io->progressFinish();

    if ($this->io->isVerbose()) {
      \uasort($parse_statistics, static fn (array $a, array $b) => $b[1] <=> $a[1]);
      $this->io->table(
        headers: ['Source ID', 'Parse Time (ms)'],
        rows: $parse_statistics,
      );
    }

    $this->io->success(\sprintf(
      'Parsing completed in %sms.',
      Timer::read('parse'),
    ));

    return $content_collection;
  }

  /**
   * {@selfdoc}
   */
  private function bundle(ContentCollection $content_collection): ExternalContentBundleCollection {
    $this->io->comment('Start bundling content.');
    Timer::start('bundle');
    $bundle_collection = $this->syncManager->bundle($content_collection);
    Timer::stop('bundle');
    $message = \sprintf(
      '%s sources bundled into %s content in %sms',
      $content_collection->count(),
      $bundle_collection->count(),
      Timer::read('bundle'),
    );
    $this->io->success($message);

    return $bundle_collection;
  }

}
