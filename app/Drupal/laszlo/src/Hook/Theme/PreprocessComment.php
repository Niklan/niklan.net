<?php

declare(strict_types=1);

namespace Drupal\laszlo\Hook\Theme;

use Drupal\comment\CommentInterface;

final readonly class PreprocessComment {

  public function __invoke(array &$variables): void {
    $comment = $variables['comment'];
    \assert($comment instanceof CommentInterface);
    $variables['author_name'] = $comment->getAuthorName();
  }

}
