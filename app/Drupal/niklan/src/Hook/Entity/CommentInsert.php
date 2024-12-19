<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\comment\CommentInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\niklan\Comment\Telegram\CommentModerationHandler;
use Symfony\Component\DependencyInjection\ContainerInterface;

final readonly class CommentInsert implements ContainerInjectionInterface {

  public function __construct(
    private CommentModerationHandler $commentModerationHandler,
  ) {}

  #[\Override]
  public static function create(ContainerInterface $container): self {
    return new self(
      $container->get(CommentModerationHandler::class),
    );
  }

  public function __invoke(EntityInterface $entity): void {
    \assert($entity instanceof CommentInterface);
    $this->commentModerationHandler->handle($entity);
  }

}
