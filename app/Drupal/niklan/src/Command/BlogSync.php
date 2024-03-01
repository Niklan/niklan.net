<?php declare(strict_types = 1);

namespace Drupal\niklan\Command;

use Drupal\niklan\Console\Style\NiklanStyle;
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
   * {@inheritdoc}
   */
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->io = new NiklanStyle($input, $output);

    $this->io->title("Start searching for files…");
    $this->io->info('Found 100500 source files…');
    $this->io->title("Start source identification process…");

    for ($i = 1; $i <= 10; $i++) {
      $this->io->advancePseudoProgress($i, 10, 'Processing file…');
    }

    return self::SUCCESS;
  }

}
