<?php

declare(strict_types=1);

namespace Drupal\app_search\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_search_results' => [
        'variables' => [
          'query' => NULL,
          'no_query' => NULL,
          'no_results' => NULL,
          'results' => [],
          'pager' => NULL,
        ],
      ],
    ];
  }

}
