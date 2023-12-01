<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Source\Collection;
use Drupal\external_content\Source\File;
use Drupal\niklan\Sync\BlogSyncManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\ProgressBar;
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
    $formatter = $this->getHelper('formatter');

    $this->welcome($output, $formatter);
    $collection = $this->find($output, $formatter);

    if (!$collection->count()) {
      $output->writeln($formatter->formatSection(
        'search',
        'cya',
        'comment',
      ));

      return self::SUCCESS;
    }

    $this->parse($collection, $output, $formatter);

    return self::SUCCESS;
  }

  /**
   * {@selfdoc}
   */
  private function welcome(OutputInterface $output, FormatterHelper $formatter): void {
    $output->writeln($formatter->formatSection(
      'start',
      'Zug! Zug! Lazy peons are ready to work!',
      'comment',
    ));
  }

  /**
   * {@selfdoc}
   */
  private function find(OutputInterface $output, FormatterHelper $formatter): Collection {
    $output->writeln($formatter->formatSection(
      'search',
      'Starting files search',
    ));

    Timer::start('finder');
    $collection = $this->syncManager->find();
    Timer::stop('finder');

    if ($collection->count()) {
      $output->writeln($formatter->formatSection(
        'search',
        \sprintf(
          '%s files found in %sms.',
          $collection->count(),
          Timer::read('finder'),
        ),
      ));
    }
    else {
      $output->writeln($formatter->formatSection(
        'search',
        'No files were found in a working directory.',
        'comment',
      ));
    }

    return $collection;
  }

  /**
   * {@selfdoc}
   */
  private function parse(Collection $collection, OutputInterface $output, FormatterHelper $formatter): void {
    $output->writeln($formatter->formatSection(
      'parse',
      'Start parsing files.',
    ));

    $progress = new ProgressBar($output, $collection->count());
    $progress->setFormat("%current%/%max% [%bar%] %elapsed% %memory%\n");

    foreach ($collection as $source) {
      \assert($source instanceof File);

      if ($output->isVerbose()) {
        $output->writeln($formatter->formatSection(
          'parse',
          \sprintf('Start parsing %s', $source->getPathname()),
        ));
      }

      Timer::start($source->id());
      // @todo Add to collection for bundling.
      $content = $this->syncManager->parse($source);
      Timer::stop($source->id());
      $progress->advance();

      if (!$output->isVerbose()) {
        continue;
      }

      $output->writeln($formatter->formatSection(
      'parse',
      \sprintf(
        'Parsing %s completed in %sms',
        $source->getPathname(),
        Timer::read($source->id()),
      ),
      ));
    }

    $progress->finish();
  }

}
