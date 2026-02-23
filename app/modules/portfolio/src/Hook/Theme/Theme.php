<?php

declare(strict_types=1);

namespace Drupal\app_portfolio\Hook\Theme;

use Drupal\Core\Hook\Attribute\Hook;

#[Hook('theme')]
final class Theme {

  public function __invoke(): array {
    return [
      'app_portfolio_list' => [
        'variables' => [
          'description' => [],
          'items' => [],
        ],
      ],
    ];
  }

}
