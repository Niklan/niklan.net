<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Data\ContentCollection;
use Drupal\external_content\Data\ExternalContentBundleCollection;
use Drupal\external_content\Data\SourceCollection;
use Drupal\external_content\Source\File;
use Drupal\niklan\Sync\BlogSyncManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressIndicator;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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
  private OutputInterface $output;

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
    $this->output = $output;

    $this->log('start', 'Zug! Zug! Lazy peons are ready to work!');
    $source_collection = $this->find();
    $content_collection = $this->parse($source_collection);
    $this->bundle($content_collection);

    return self::SUCCESS;
  }

  /**
   * {@selfdoc}
   */
  private function find(): SourceCollection {
    $this->log('search', 'Starting sources search');

    Timer::start('finder');
    $collection = $this->syncManager->find();
    Timer::stop('finder');

    if ($collection->count()) {
      $message = \sprintf(
        '%s sources found in %sms.',
        $collection->count(),
        Timer::read('finder'),
      );
      $this->log('search', $message);
    }
    else {
      $this->log('search', 'No sources were found in a working directory.');
    }

    return $collection;
  }

  /**
   * {@selfdoc}
   */
  private function parse(SourceCollection $collection): ContentCollection {
    $parse_statistics = [];

    $this->log('parse', 'Start parsing sources.');

    $progress = $this->prepareProgress();
    $progress->start('Parsing sources…');
    Timer::start('parse');
    $content_collection = new ContentCollection();

    foreach ($collection as $source) {
      \assert($source instanceof File);
      $timer_id = "parse_{$source->id()}";
      Timer::start($timer_id);
      $content_collection->add($this->syncManager->parse($source));
      Timer::stop($timer_id);
      $progress->advance();
      $parse_statistics[$source->id()] = Timer::read($timer_id);
    }

    Timer::stop('parse');
    $progress->finish(\sprintf(
      'Parsing completed in %sms',
      Timer::read('parse'),
    ));

    if ($this->output->isVerbose()) {
      foreach ($parse_statistics as $source_id => $parse_time) {
        $message = \sprintf('Parsing %s took %sms', $source_id, $parse_time);
        $this->log('parse', $message);
      }
    }

    return $content_collection;
  }

  /**
   * {@selfdoc}
   */
  private function prepareProgress(): ProgressIndicator {
    return new ProgressIndicator(
      output: $this->output,
      indicatorValues: [
        '[ o o o o o ]',
        '[co o o o o ]',
        '[Co o o o o ]',
        '[-c o o o o ]',
        '[-C o o o o ]',
        '[--co o o o ]',
        '[--Co o o o ]',
        '[---c o o o ]',
        '[---C o o o ]',
        '[----co o o ]',
        '[----Co o o ]',
        '[-----c o o ]',
        '[-----C o o ]',
        '[------co o ]',
        '[------Co o ]',
        '[-------c o ]',
        '[-------C o ]',
        '[--------co ]',
        '[--------Co ]',
        '[---------c ]',
        '[---------C ]',
        '[----------c]',
        '[----------C]',
        '[-----------]',
      ],
    );
  }

  /**
   * {@selfdoc}
   */
  private function log(string $type, string $message): void {
    $formatter = $this->getHelper('formatter');
    $this->output->writeln($formatter->formatSection(
      section: $type,
      message: $message,
    ));
  }

  /**
   * {@selfdoc}
   */
  private function bundle(ContentCollection $content_collection): ExternalContentBundleCollection {
    $this->log('bundle', 'Start bundling content');
    Timer::start('bundle');
    $bundle_collection = $this->syncManager->bundle($content_collection);
    Timer::stop('bundle');
    $message = \sprintf(
      '%s sources bundled into %s content in %sms',
      $content_collection->count(),
      $bundle_collection->count(),
      Timer::read('bundle'),
    );
    $this->log('bundle', $message);

    return $bundle_collection;
  }

}
