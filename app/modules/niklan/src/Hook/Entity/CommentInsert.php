<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Entity;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Hook\Attribute\Hook;
use Drupal\niklan\Comment\Telegram\CommentModerationHandler;

#[Hook('comment_insert')]
final readonly class CommentInsert {

  public function __construct(
    private CommentModerationHandler $commentModerationHandler,
  ) {}

  public function __invoke(EntityInterface $entity): void {
    \assert($entity instanceof CommentInterface);
    $this->commentModerationHandler->handle($entity);
  }

}
