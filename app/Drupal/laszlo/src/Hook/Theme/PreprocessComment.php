<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\comment\CommentInterface;

final readonly class PreprocessComment {

  public function __invoke(array &$variables): void {
    $comment = $variables['comment'];
    \assert($comment instanceof CommentInterface);
    $commented_entity = $comment->getCommentedEntity();

    $variables['author_name'] = $comment->getAuthorName();
    $variables['created_timestamp'] = $comment->getCreatedTime();

    if ($commented_entity) {
      $variables['anchor'] = "comment-{$comment->id()}";
      $variables['permalink_url'] = $commented_entity->toUrl()->setOption('fragment', $variables['anchor'])->toString();
    }

    $variables['homepage'] = NULL;

    if ($comment->get('homepage')->isEmpty()) {
      return;
    }

    $variables['homepage'] = $comment->get('homepage')->getString();
  }

}
