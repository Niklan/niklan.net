<?php

declare(strict_types=1);

namespace Drupal\niklan\Telegram\Controller;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Logger\LoggerChannelInterface;
use Drupal\niklan\Telegram\Telegram;
use SergiX44\Nutgram\Telegram\Types\Common\Update;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class WebhookController implements ContainerInjectionInterface {

  public function __construct(
    private Telegram $telegram,
    private LoggerChannelInterface $logger,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(Telegram::class),
      $container->get('logger.channel.niklan.telegram'),
    );
  }

  public function __invoke(Request $request): JsonResponse {
    $this->logger->info('Webhook received: @request', ['@request' => \json_encode($request->toArray())]);
    $on_update = fn (Update $update) => $this->logger->info('Update received: @update', ['@update' => \json_encode($update->toArray())]);
    $this->telegram->getBot()->fallback($on_update);

    return new JsonResponse(TRUE);
  }

}
