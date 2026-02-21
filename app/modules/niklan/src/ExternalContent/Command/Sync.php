<?php

declare(strict_types=1);

namespace Drupal\niklan\ExternalContent\Command;

use Drupal\Core\Cache\CacheTagsInvalidatorInterface;
use Drupal\Core\Site\Settings;
use Drupal\niklan\Console\Log\ConsoleLogger;
use Drupal\niklan\ExternalContent\Domain\SyncContext;
use Drupal\niklan\ExternalContent\Pipeline\ArticleSyncPipeline;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'niklan:blog:sync', description: 'Sync blog articles.')]
final class Sync extends Command {

  public const string CACHE_TAG = 'niklan:content_sync';

  public function __construct(
    private readonly LoggerInterface $logger,
    private readonly ArticleSyncPipeline $syncPipeline,
    private readonly CacheTagsInvalidatorInterface $cacheTagsInvalidator,
  ) {
    parent::__construct();
  }

  protected function configure(): void {
    $this->addArgument(
      name: 'source-uri',
      mode: InputArgument::OPTIONAL,
      description: 'Absolute path or path relative to the content root. If omitted, the content root is used.',
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

    $content_root = Settings::get('external_content_directory');
    \assert(\is_string($content_root));
    $source_uri = $input->getArgument('source-uri');
    \assert(\is_string($source_uri) || \is_null($source_uri));
    $working_directory = match (TRUE) {
      $source_uri === NULL => $content_root,
      // Resolve relative paths against the content root.
      !\str_starts_with($source_uri, '/') && !\str_contains($source_uri, '://') => $content_root . '/' . $source_uri,
      default => $source_uri,
    };

    $context = new SyncContext($working_directory, $content_root, $logger);
    $context->setForceStatus((bool) $input->getOption('force'));
    $this->syncPipeline->run($context);
    $this->cacheTagsInvalidator->invalidateTags([self::CACHE_TAG]);

    return self::SUCCESS;
  }

}
