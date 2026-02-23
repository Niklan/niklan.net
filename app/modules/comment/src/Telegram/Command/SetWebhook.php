<?php

declare(strict_types=1);

namespace Drupal\app_comment\Telegram\Command;

use Drupal\app_comment\Telegram\Telegram;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'app:comment:telegram:set-webhook',
  description: 'Sets Telegram webhook.',
  aliases: ['niklan:telegram:set-webhook'],
)]
final class SetWebhook extends Command {

  public function __construct(
    private readonly Telegram $telegram,
  ) {
    parent::__construct();
  }

  #[\Override]
  protected function execute(InputInterface $input, OutputInterface $output): int {
    if (!$this->telegram->isConfigured()) {
      $output->writeln('<error>Telegram is not configured.</error>');

      return self::FAILURE;
    }

    $this->telegram->setWebhook();

    return self::SUCCESS;
  }

}
