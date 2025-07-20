<?php

declare(strict_types=1);

namespace Drupal\niklan\Hook\Theme;

use Drupal\comment\CommentInterface;
use Drupal\Core\Entity\Display\EntityViewDisplayInterface;
use Drupal\Core\Hook\Attribute\Hook;

#[Hook('comment_view')]
final readonly class CommentView {

  public function __invoke(array &$build, CommentInterface $comment, EntityViewDisplayInterface $display, string $view_mode): void {
    // Disable threading for all situations. This is done through the theme and
    // a custom formatter. Unfortunately, it breaks a lot of things and is
    // hardcoded into the system using a pre-render.
    $build['#comment_threaded'] = FALSE;
    // Reset inner counters which append unneeded '</div>'.
    $build['#comment_indent_final'] = 0;
  }

}
