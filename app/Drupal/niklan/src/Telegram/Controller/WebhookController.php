<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\niklan\Telegram\Telegram;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final readonly class WebhookController implements ContainerInjectionInterface {

  public function __construct(
    private Telegram $telegram,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(Telegram::class),
    );
  }

  public function __invoke(Request $request): Response {
    $this->telegram->getBot()->run();

    // Telegram expects any status code from 200 to 299.
    // https://github.com/tdlib/telegram-bot-api/blob/5d88023dd1e65b7d0926a71aea4487d6cac3bf13/telegram-bot-api/WebhookActor.cpp#L619
    return new Response();
  }

}
