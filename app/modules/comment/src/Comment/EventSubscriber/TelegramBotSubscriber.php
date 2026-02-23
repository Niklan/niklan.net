<?php

declare(strict_types=1);

namespace Drupal\app_comment\Comment\EventSubscriber;

use Drupal\app_comment\Comment\Telegram\CommentModerationHandler;
use Drupal\app_comment\Telegram\Event\TelegramBotInitializationEvent;
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
