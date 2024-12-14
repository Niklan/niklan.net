<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram\Event;

use SergiX44\Nutgram\Nutgram;
use Symfony\Contracts\EventDispatcher\Event;

final class TelegramBotInitializationEvent extends Event {

  public function __construct(
    public readonly Nutgram $bot,
  ) {}

}
