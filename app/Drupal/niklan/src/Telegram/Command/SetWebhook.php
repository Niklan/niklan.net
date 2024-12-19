<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram\Command;

use Drupal\niklan\Telegram\Telegram;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
  name: 'niklan:telegram:set-webhook',
  description: 'Sets Telegram webhook.',
)]
final class SetWebhook extends Command {

  public function __construct(
    private readonly Telegram $telegram,
  ) {
    parent::__construct();
  }

  #[\Override]
  protected function execute(InputInterface $input, OutputInterface $output): int {
    $this->telegram->setWebhook();

    return self::SUCCESS;
  }

}
