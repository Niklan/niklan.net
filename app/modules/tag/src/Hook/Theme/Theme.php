<?php

declare(strict_types=1);

namespace Drupal\app_tag\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_tag_list' => [
        'variables' => [
          'items' => [],
        ],
      ],
    ];
  }

}
