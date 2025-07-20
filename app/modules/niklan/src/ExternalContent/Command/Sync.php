<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Command;

use Composer\Console\Input\InputOption;
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
    private readonly LoggerInterface $logger,
    private readonly ArticleSyncPipeline $syncPipeline,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addArgument(
      name: 'source-uri',
      mode: InputArgument::OPTIONAL,
      description: 'The source content URI. If omitted, the default source URI will be used.',
      default: Settings::get('external_content_directory'),
    );

    $this->addOption(
      name: 'force',
      shortcut: 'f',
      mode: InputOption::VALUE_NONE,
      description: 'Force sync even if up-to-date.',
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    $output->writeln('Start syncing...');
    $logger = new ConsoleLogger($this->logger, $output);

    $source_uri = $input->getArgument('source-uri') ?? Settings::get('external_content_directory');
    \assert(\is_string($source_uri));

    $context = new SyncContext($source_uri, $logger);
    $context->setForceStatus((bool) $input->getOption('force'));
    $this->syncPipeline->run($context);

    return self::SUCCESS;
  }

}
