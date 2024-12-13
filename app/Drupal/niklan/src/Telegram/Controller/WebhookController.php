<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram\Controller;

use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Longman\TelegramBot\Telegram;
use Symfony\Component\HttpFoundation\JsonResponse;

final readonly class WebhookController {

  public function __invoke(): JsonResponse {
    return new JsonResponse(['True']);
  }

  public static function setWebhook(): void {
    $webhook_url = Url::fromRoute('niklan.telegram.webhook')->setAbsolute()->toString();
    $telegram = new Telegram(Settings::get('telegram_comment_moderate_bot_token'));
    $telegram->setWebhook($webhook_url, [
      'secret_token' => Settings::get('telegram_bot_secret_token'),
      'allowed_updates' => ['callback_query'],
    ]);
  }

}