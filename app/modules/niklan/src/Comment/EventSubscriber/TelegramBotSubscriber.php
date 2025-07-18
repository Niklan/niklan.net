<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\EventSubscriber;

use Drupal\niklan\Comment\Telegram\CommentModerationHandler;
use Drupal\niklan\Telegram\Event\TelegramBotInitializationEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final readonly class TelegramBotSubscriber implements EventSubscriberInterface {

  public function __construct(
    private CommentModerationHandler $commentModerationHandler,
  ) {}

  public function onTelegramBotInitialization(TelegramBotInitializationEvent $event): void {
    $event->bot->onCallbackQuery($this->commentModerationHandler->onCallbackQuery(...));
  }

  #[\Override]
  public static function getSubscribedEvents(): array {
    return [
      TelegramBotInitializationEvent::class => 'onTelegramBotInitialization',
    ];
  }

}
