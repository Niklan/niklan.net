<?php

declare(strict_types=1);

namespace Drupal\niklan\Comment\Telegram;

enum CommentModerationCallbackType: string {

  case Approve = 'comment-moderation:approve';
  case Delete = 'comment-moderation:delete';
  case DeleteConfirm = 'comment-moderation:delete-confirm';
  case DeleteCancel = 'comment-moderation:delete-cancel';

  public function buildCallbackId(string $comment_id): string {
    return $this->value . ':' . $comment_id;
  }

}
