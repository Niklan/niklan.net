<?php

declare(strict_types=1);

namespace Drupal\app_comment;

use Drupal\app_comment\Comment\Controller\CommentReply;
use Drupal\app_comment\Comment\EventSubscriber\RouteAlter;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\DependencyInjection\ServiceProviderInterface;

final readonly class AppCommentServiceProvider implements ServiceProviderInterface {

  #[\Override]
  public function register(ContainerBuilder $container): void {
    $autowire = static fn (string $class) => $container
      ->autowire($class)
      ->setPublic(boolean: TRUE)
      ->setAutoconfigured(autoconfigured: TRUE);

    $autowire(RouteAlter::class);
    $autowire(CommentReply::class);
  }

}
