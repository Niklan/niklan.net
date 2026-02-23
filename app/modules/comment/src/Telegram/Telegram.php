<?php

declare(strict_types=1);

namespace Drupal\app_comment\Telegram;

use Drupal\app_comment\Telegram\Event\TelegramBotInitializationEvent;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Psr\EventDispatcher\EventDispatcherInterface;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class Telegram {

  private ?Nutgram $bot = NULL;

  public function __construct(
    #[Autowire(service: 'logger.channel.app_comment.telegram')]
    private readonly LoggerChannelInterface $logger,
    private readonly EventDispatcherInterface $eventDispatcher,
  ) {}

  public function setWebhook(): void {
    $this->getBot()->setWebhook(
      url: Url::fromRoute('app_comment.telegram.webhook')->setAbsolute()->toString(),
      secret_token: $this->getSecretToken(),
    );
  }

  public function getBot(): Nutgram {
    if ($this->bot === NULL) {
      $this->bot = $this->initBot();
    }

    return $this->bot;
  }

  public function isConfigured(): bool {
    $errors = [];

    try {
      $this->getChatId();
    }
    catch (\InvalidArgumentException $e) {
      $errors[] = $e->getMessage();
    }

    try {
      $this->getToken();
    }
    catch (\InvalidArgumentException $e) {
      $errors[] = $e->getMessage();
    }

    try {
      $this->getSecretToken();
    }
    catch (\InvalidArgumentException $e) {
      $errors[] = $e->getMessage();
    }

    if (\count($errors) > 0) {
      $this->logger->error('Telegram is not configured: ' . \implode('; ', $errors));

      return FALSE;
    }

    return TRUE;
  }

  public function getChatId(): string {
    $chat_id = Settings::get('telegram_chat_id');

    if (!\is_string($chat_id)) {
      throw new \InvalidArgumentException('Telegram chat ID is not set or has invalid type.');
    }

    return $chat_id;
  }

  public function getSecretToken(): string {
    $secret_token = Settings::get('telegram_secret_token');

    if (!\is_string($secret_token)) {
      throw new \InvalidArgumentException('Telegram secret token is not set');
    }

    return $secret_token;
  }

  public function getToken(): string {
    $token = Settings::get('telegram_token');

    if (!\is_string($token)) {
      throw new \InvalidArgumentException('Telegram token is not set');
    }

    return $token;
  }

  private function initBot(): Nutgram {
    $webhook = new Webhook(secretToken: $this->getSecretToken());
    $webhook->setSafeMode(TRUE);
    $config = new Configuration(logger: $this->logger);
    $bot = new Nutgram($this->getToken(), $config);
    $bot->setRunningMode($webhook);

    $event = new TelegramBotInitializationEvent($bot);
    $this->eventDispatcher->dispatch($event);

    return $bot;
  }

}
