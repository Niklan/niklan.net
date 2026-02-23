<?php

declare(strict_types=1);

namespace Drupal\app_comment\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_comment_list' => [
        'variables' => [
          'heading' => NULL,
          'items' => [],
        ],
      ],
      'app_comment_thread' => [
        'variables' => [
          'depth' => 0,
          'thread_id' => NULL,
          'comments' => [],
        ],
      ],
      'app_comment_reply_page' => [
        'variables' => [
          'children' => [],
        ],
      ],
    ];
  }

}
