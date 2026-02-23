<?php

declare(strict_types=1);

namespace Drupal\app_comment;

use Drupal\app_comment\Comment\Controller\CommentReply;
use Drupal\app_comment\Comment\EventSubscriber\RouteAlter;
use Drupal\app_comment\Comment\EventSubscriber\TelegramBotSubscriber;
use Drupal\app_comment\Comment\Telegram\CommentModerationHandler;
use Drupal\app_comment\Telegram\Controller\WebhookController;
use Drupal\app_comment\Telegram\Telegram;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;
use Symfony\Component\DependencyInjection\ChildDefinition;

final readonly class AppCommentServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(TRUE)
      ->setAutoconfigured(TRUE);

    $container->setDefinition(
      id: 'logger.channel.app_comment.telegram',
      definition: (new ChildDefinition('logger.channel_base'))->addArgument('app_comment.telegram'),
    );

    $autowire(Telegram::class);
    $autowire(CommentModerationHandler::class);
    $autowire(RouteAlter::class);
    $autowire(TelegramBotSubscriber::class);
    $autowire(WebhookController::class);
    $autowire(CommentReply::class);
  }

}
