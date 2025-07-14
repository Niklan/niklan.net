<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Command;

use Drupal\Core\Site\Settings;
use Drupal\niklan\Console\Log\ConsoleLogger;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'niklan:blog:sync', description: 'Sync blog articles.')]
final class Sync extends Command {

  public function __construct(
    private LoggerInterface $logger,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addArgument(
      name: 'sourceUri',
      mode: InputArgument::OPTIONAL,
      description: 'The source content URI. If omitted, the default source URI will be used.',
      default: Settings::get('external_content_directory'),
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $output->writeln('Start syncing...');
    $logger = new ConsoleLogger($this->logger, $output);

    $pipeline = new ArticleSyncPipeline();
    $pipeline->run(new SyncContext($input->getArgument('sourceUri'), $logger));
    return self::SUCCESS;
  }

}
