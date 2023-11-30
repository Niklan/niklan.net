<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\Component\Utility\Timer;
use Drupal\external_content\Source\Collection;
use Drupal\niklan\Sync\BlogSyncManager;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\FormatterHelper;
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
    $formatter = $this->getHelper('formatter');
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

}
