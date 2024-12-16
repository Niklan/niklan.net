<?php

/**
 * @file
 * \Drupal\niklan\Telegram\Controller\WebhookController::setWebhook();
 */

use Drupal\comment\Entity\Comment;
use Drupal\niklan\Comment\Telegram\CommentModerationHandler;

$handler = Drupal::service(CommentModerationHandler::class);
assert($handler instanceof CommentModerationHandler);
$comment = Comment::load(5246);

$handler->handle($comment);