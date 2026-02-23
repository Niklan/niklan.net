<?php

declare(strict_types=1);

namespace Drupal\app_comment\Telegram\Controller;

use Drupal\app_comment\Telegram\Telegram;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class WebhookController {

  public function __construct(
    private Telegram $telegram,
  ) {}

  public function __invoke(Request $request): Response {
    $this->telegram->getBot()->run();

    // Telegram expects any status code from 200 to 299.
    // https://github.com/tdlib/telegram-bot-api/blob/5d88023dd1e65b7d0926a71aea4487d6cac3bf13/telegram-bot-api/WebhookActor.cpp#L619
    return new Response();
  }

}
