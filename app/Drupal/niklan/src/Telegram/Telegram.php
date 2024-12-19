<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\niklan\Telegram\Event\TelegramBotInitializationEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class Telegram {

  private ?Nutgram $bot = NULL;

  public function __construct(
    #[Autowire(service: 'logger.channel.niklan.telegram')]
    private readonly LoggerChannelInterface $logger,
    private readonly EventDispatcherInterface $eventDispatcher,
  ) {}

  public function setWebhook(): void {
    $this->getBot()->setWebhook(
      url: Url::fromRoute('niklan.telegram.webhook')->setAbsolute()->toString(),
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
    return $this->getChatId() !== NULL && $this->getSecretToken() !== NULL && $this->getChatId() !== NULL;
  }

  public function getChatId(): ?string {
    return Settings::get('telegram_chat_id');
  }

  public function getSecretToken(): ?string {
    return Settings::get('telegram_secret_token');
  }

  public function getToken(): ?string {
    return Settings::get('telegram_token');
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
