<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Command;

use Drupal\Core\Site\Settings;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'niklan:external-content:sync',
  description: 'Sync external content from remote sources.',
)]
final class Sync extends Command {

  protected function configure(): void {
    $this->addArgument(
      name: 'sourceUri',
      mode: InputArgument::OPTIONAL,
      description: 'The source content URI. If omitted, the default source URI will be used.',
      default: Settings::get('external_content_directory'),
    );
  }

  protected function execute(InputInterface $input, OutputInterface $output): int {
    // @todo Add proper DI.
    $output->writeln('Start syncing...');
    $pipeline = new ArticleSyncPipeline();
    // @todo Add logger decorator with CLI support.
    $pipeline->run(new SyncContext($input->getArgument('sourceUri'), \Drupal::logger('niklan.external_content')));
    return self::SUCCESS;
  }

}
