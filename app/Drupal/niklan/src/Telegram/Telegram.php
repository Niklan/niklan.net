<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram;

use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\Core\Site\Settings;
use Drupal\Core\Url;
use Drupal\niklan\Telegram\Event\TelegramBotInitializationEvent;
use SergiX44\Nutgram\Configuration;
use SergiX44\Nutgram\Nutgram;
use SergiX44\Nutgram\RunningMode\Webhook;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\EventDispatcher\EventDispatcher;

final class Telegram {

  private ?Nutgram $bot = NULL;

  public function __construct(
    #[Autowire(service: 'logger.channel.niklan.telegram')]
    private readonly LoggerChannelInterface $logger,
    private readonly EventDispatcher $eventDispatcher,
  ) {}

  public function getBot(): Nutgram {
    if ($this->bot === NULL) {
      $this->bot = $this->initBot();
    }

    return $this->bot;
  }

  public function registerWebhook(): void {
    $this->getBot()->setWebhook(
      url: Url::fromRoute('niklan.telegram.webhook')->setAbsolute()->toString(),
      secret_token: Settings::get('telegram_secret_token'),
    );
  }

  private function initBot(): Nutgram {
    $webhook = new Webhook(secretToken: Settings::get('telegram_secret_token'));
    $webhook->setSafeMode(TRUE);
    $config = new Configuration(logger: $this->logger);
    $bot = new Nutgram(Settings::get('telegram_token'), $config);
    $bot->setRunningMode($webhook);

    $event = new TelegramBotInitializationEvent($bot);
    $this->eventDispatcher->dispatch($event);

    return $bot;
  }

}
